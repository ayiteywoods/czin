<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\PaystackPaymentReconciliationService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        PaystackPaymentReconciliationService $reconciliation,
    ) {
        $signature = (string) $request->header('x-paystack-signature', '');
        $payload = (string) $request->getContent();

        if ($signature === '' || $payload === '') {
            return response()->noContent();
        }

        $expected = app(PaystackService::class)->computeWebhookSignature($payload);

        if (! hash_equals($expected, $signature)) {
            Log::warning('Paystack webhook signature mismatch.', [
                'paystack_mode' => app(PaystackService::class)->mode(),
            ]);

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

        $payment = $reconciliation->resolvePayment($reference, $data);

        if (! $payment || $payment->status === PaymentStatus::Paid) {
            return response()->noContent();
        }

        $order = $payment->order;

        if (! $order) {
            return response()->noContent();
        }

        $payment->update([
            'metadata' => array_merge($payment->metadata ?? [], [
                'webhook' => $event,
            ]),
        ]);

        $result = $reconciliation->applySuccessfulPaystackPayment($order, $payment, $data);

        if (! $result['reconciled']) {
            Log::error('Paystack webhook payment verified but order not marked paid.', [
                'order_id' => $order->id,
                'reference' => $reference,
                'reason' => $result['reason'],
            ]);
        }

        return response()->noContent();
    }
}
