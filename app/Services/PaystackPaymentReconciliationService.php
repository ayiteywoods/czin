<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaystackPaymentReconciliationService
{
    public function __construct(
        protected PaystackService $paystack,
        protected OrderPaymentService $orderPayments,
    ) {}

    /**
     * @return array{reconciled: bool, reason: string}
     */
    public function reconcilePayment(Payment $payment, bool $verbose = false): array
    {
        if ($payment->provider !== 'paystack') {
            return ['reconciled' => false, 'reason' => 'not a Paystack payment'];
        }

        $order = $payment->order;

        if (! $order) {
            return ['reconciled' => false, 'reason' => 'order not found'];
        }

        if ($order->payment_status === PaymentStatus::Paid) {
            return ['reconciled' => true, 'reason' => 'order already paid'];
        }

        if ($order->payment_status === PaymentStatus::Refunded) {
            return ['reconciled' => false, 'reason' => 'order refunded'];
        }

        if ($payment->status === PaymentStatus::Paid) {
            $this->orderPayments->markAsPaid($order, $payment, (array) data_get($payment->metadata, 'verification', []));

            return [
                'reconciled' => $order->fresh()->payment_status === PaymentStatus::Paid,
                'reason' => 'synced paid payment record to order',
            ];
        }

        try {
            $data = $this->paystack->verify($payment->reference);
        } catch (Throwable $exception) {
            Log::warning('Paystack payment reconciliation verify failed.', [
                'payment_id' => $payment->id,
                'reference' => $payment->reference,
                'error' => $exception->getMessage(),
            ]);

            return ['reconciled' => false, 'reason' => 'verify failed: '.$exception->getMessage()];
        }

        if (($data['status'] ?? null) !== 'success') {
            return ['reconciled' => false, 'reason' => 'Paystack status: '.($data['status'] ?? 'unknown')];
        }

        $this->orderPayments->markAsPaid($order, $payment, $data);

        return [
            'reconciled' => $order->fresh()->payment_status === PaymentStatus::Paid,
            'reason' => 'verified with Paystack',
        ];
    }

    public function reconcileOrder(Order $order, bool $searchPaystack = true): bool
    {
        $payments = Payment::query()
            ->where('order_id', $order->id)
            ->where('provider', 'paystack')
            ->orderByDesc('id')
            ->get();

        foreach ($payments as $payment) {
            if ($this->reconcilePayment($payment)['reconciled']) {
                return true;
            }
        }

        if (! $searchPaystack || $order->payment_status === PaymentStatus::Paid) {
            return false;
        }

        return $this->reconcileOrderFromPaystackTransactions($order);
    }

    public function reconcileByReference(string $reference): bool
    {
        try {
            $data = $this->paystack->verify($reference);
        } catch (Throwable) {
            return false;
        }

        if (($data['status'] ?? null) !== 'success') {
            return false;
        }

        $payment = $this->findPaymentByReference($reference)
            ?? $this->recoverPaymentFromPaystackData($reference, $data);

        if (! $payment) {
            return false;
        }

        return $this->reconcilePayment($payment)['reconciled'];
    }

    protected function reconcileOrderFromPaystackTransactions(Order $order): bool
    {
        $from = $order->created_at?->copy()->subDay() ?? now()->subDays(30);
        $page = 1;

        do {
            try {
                $transactions = $this->paystack->listTransactions([
                    'status' => 'success',
                    'from' => $from->toIso8601String(),
                    'to' => now()->toIso8601String(),
                    'perPage' => 100,
                    'page' => $page,
                ]);
            } catch (Throwable $exception) {
                Log::warning('Paystack transaction list failed during reconciliation.', [
                    'order_id' => $order->id,
                    'error' => $exception->getMessage(),
                ]);

                return false;
            }

            foreach ($transactions as $transaction) {
                $orderId = data_get($transaction, 'metadata.order_id');
                $orderNumber = data_get($transaction, 'metadata.order_number');

                if ((string) $orderId !== (string) $order->id && $orderNumber !== $order->order_number) {
                    continue;
                }

                $reference = (string) ($transaction['reference'] ?? '');

                if ($reference === '') {
                    continue;
                }

                $payment = $this->findPaymentByReference($reference)
                    ?? $this->recoverPaymentFromPaystackData($reference, $transaction);

                if ($payment && $this->reconcilePayment($payment, false)['reconciled']) {
                    return true;
                }
            }

            $page++;
        } while (count($transactions) === 100 && $page <= 5);

        return false;
    }

    public function findPaymentByReference(string $reference): ?Payment
    {
        return Payment::query()->where('reference', $reference)->first();
    }

    public function recoverPaymentFromPaystackData(string $reference, array $data): ?Payment
    {
        $existing = $this->findPaymentByReference($reference);

        if ($existing) {
            return $existing;
        }

        $orderId = data_get($data, 'metadata.order_id');
        $orderNumber = data_get($data, 'metadata.order_number');

        $order = Order::query()
            ->when($orderId, fn ($query) => $query->whereKey($orderId))
            ->when(! $orderId && $orderNumber, fn ($query) => $query->where('order_number', $orderNumber))
            ->first();

        if (! $order) {
            Log::warning('Paystack payment could not be matched to an order.', [
                'reference' => $reference,
                'order_id' => $orderId,
                'order_number' => $orderNumber,
            ]);

            return null;
        }

        return Payment::query()->create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'reference' => $reference,
            'provider' => 'paystack',
            'provider_transaction_id' => isset($data['id']) ? (string) $data['id'] : null,
            'amount' => isset($data['amount']) ? ((float) $data['amount'] / 100) : $order->total,
            'currency' => $data['currency'] ?? config('shop.currency'),
            'channel' => $data['channel'] ?? data_get($data, 'authorization.channel'),
            'status' => PaymentStatus::Pending,
            'metadata' => [
                'recovered' => true,
            ],
        ]);
    }
}
