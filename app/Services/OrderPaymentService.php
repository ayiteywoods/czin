<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderPaymentService
{
    public function __construct(
        protected OrderNotificationService $notifications,
        protected StockReservationService $stock
    ) {}

    public function markAsPaid(Order $order, Payment $payment, array $data = []): void
    {
        $wasAlreadyPaid = false;
        $stockUnavailable = false;
        $wasCancelled = false;

        DB::transaction(function () use ($order, $payment, $data, &$wasAlreadyPaid, &$stockUnavailable, &$wasCancelled) {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $payment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($order->payment_status === PaymentStatus::Paid) {
                $wasAlreadyPaid = true;

                return;
            }

            if ($order->payment_status === PaymentStatus::Refunded || $order->status === OrderStatus::Refunded) {
                return;
            }

            $wasCancelled = $order->status === OrderStatus::Cancelled;

            $order->loadMissing('items');

            try {
                $this->stock->fulfillOrderItems($order->items);
            } catch (InsufficientStockException $exception) {
                $stockUnavailable = true;

                Log::warning('Payment received but stock unavailable; order marked paid for manual fulfillment.', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'message' => $exception->getMessage(),
                ]);
            }

            $paidAt = isset($data['paid_at'])
                ? Carbon::parse($data['paid_at'])
                : now();

            $transactionId = isset($data['id']) ? (string) $data['id'] : $payment->provider_transaction_id;
            $receiptNumber = filled($data['receipt_number'] ?? null)
                ? (string) $data['receipt_number']
                : $transactionId;
            $displayReference = $receiptNumber
                ? $order->order_number.'-'.$receiptNumber
                : null;

            $paymentMetadata = array_merge($payment->metadata ?? [], [
                'verification' => $data,
                'display_reference' => $displayReference,
            ]);

            if ($stockUnavailable) {
                $paymentMetadata['stock_warning'] = true;
                $paymentMetadata['stock_warning_message'] = 'Stock was unavailable when payment was confirmed. Fulfill manually.';
            }

            $payment->update([
                'status' => PaymentStatus::Paid,
                'channel' => $data['channel'] ?? data_get($data, 'authorization.channel') ?? $payment->channel,
                'provider_transaction_id' => $transactionId ?: $payment->provider_transaction_id,
                'paid_at' => $paidAt,
                'metadata' => $paymentMetadata,
            ]);

            $order->update([
                'payment_status' => PaymentStatus::Paid,
                'status' => OrderStatus::Paid,
                'paid_at' => $paidAt,
            ]);

            if ($wasCancelled) {
                Log::info('Reinstated cancelled order after confirmed payment.', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'payment_id' => $payment->id,
                ]);
            }
        });

        if ($wasAlreadyPaid) {
            return;
        }

        $order->refresh();
        $payment->refresh();

        app(CartService::class)->clearOrderItems($order);

        $this->notifications->paymentReceived($order);
        app(AdminNotificationService::class)->sync();
    }
}
