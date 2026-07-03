<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderPaymentService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __invoke(Request $request, OrderPaymentService $payments)
    {
        $signature = (string) $request->header('x-paystack-signature', '');
        $payload = (string) $request->getContent();

        if ($signature === '' || $payload === '') {
            return response()->noContent();
        }

        $expected = app(PaystackService::class)->computeWebhookSignature($payload);

        if (! hash_equals($expected, $signature)) {
            Log::warning('Paystack webhook signature mismatch');

            return response()->noContent();
        }

        $event = $request->json()->all();
        $eventType = $event['event'] ?? null;
        $data = $event['data'] ?? [];

        if ($eventType !== 'charge.success') {
            return response()->noContent();
        }

        $reference = $data['reference'] ?? null;

        if (! $reference) {
            return response()->noContent();
        }

        $payment = Payment::query()->where('reference', $reference)->first()
            ?? $this->paymentFromWebhookData($reference, $data);

        if (! $payment || $payment->status === PaymentStatus::Paid) {
            return response()->noContent();
        }

        $order = $payment->order;

        if (! $order || $order->status === OrderStatus::Cancelled) {
            return response()->noContent();
        }

        $payment->update([
            'metadata' => array_merge($payment->metadata ?? [], [
                'webhook' => $event,
            ]),
        ]);

        $payments->markAsPaid($order, $payment, $data);

        return response()->noContent();
    }

    protected function paymentFromWebhookData(string $reference, array $data): ?Payment
    {
        $orderId = data_get($data, 'metadata.order_id');
        $orderNumber = data_get($data, 'metadata.order_number');

        $order = Order::query()
            ->when($orderId, fn ($query) => $query->whereKey($orderId))
            ->when(! $orderId && $orderNumber, fn ($query) => $query->where('order_number', $orderNumber))
            ->first();

        if (! $order) {
            Log::warning('Paystack webhook payment could not be matched to an order.', [
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
                'webhook_recovered' => true,
            ],
        ]);
    }
}
