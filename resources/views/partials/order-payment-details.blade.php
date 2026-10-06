@php($payment = $order->payment)

<dl class="space-y-3 text-sm">
    <div>
        <dt class="text-brand-muted">Payment method</dt>
        <dd class="font-medium">{{ $order->receiptPaymentMethodLabel() }}</dd>
    </div>
    <div>
        <dt class="text-brand-muted">Payment status</dt>
        <dd class="font-medium">{{ $order->payment_status->label() }}</dd>
    </div>

    @if ($payment?->paystackReference())
        <div>
            <dt class="text-brand-muted">Paystack reference</dt>
            <dd class="font-medium break-all">{{ $payment->paystackReference() }}</dd>
        </div>
    @endif
    @if ($payment?->paystackTransactionId())
        <div>
            <dt class="text-brand-muted">Paystack transaction ID</dt>
            <dd class="font-medium break-all">{{ $payment->paystackTransactionId() }}</dd>
        </div>
    @endif
    @if ($payment?->paystackChannel() && strtolower((string) $order->payment_method) === 'paystack')
        <div>
            <dt class="text-brand-muted">Paystack channel</dt>
            <dd class="font-medium">{{ $order->paymentChannelLabel() }}</dd>
        </div>
    @endif
    @if ($payment?->paid_at)
        <div>
            <dt class="text-brand-muted">Paid at</dt>
            <dd class="font-medium">{{ $payment->paid_at->format('M j, Y g:i A') }}</dd>
        </div>
    @endif
</dl>

@if (! $payment && ! filled($order->payment_method))
    <p class="mt-3 text-sm text-brand-muted">No payment record yet.</p>
@endif
