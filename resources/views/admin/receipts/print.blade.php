<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            line-height: 1.45;
            color: #111;
            background: #fff;
        }
        .receipt { max-width: 72mm; margin: 0 auto; }
        .center { text-align: center; }
        .title { font-size: 16px; font-weight: 700; letter-spacing: 0.04em; }
        .muted { color: #555; }
        .divider { border-top: 1px dashed #999; margin: 10px 0; }
        .meta { margin: 8px 0; }
        .meta strong { display: block; }
        .items { width: 100%; border-collapse: collapse; }
        .items td { padding: 5px 0; vertical-align: top; }
        .qty { width: 24px; font-weight: 700; white-space: nowrap; }
        .price { text-align: right; white-space: nowrap; }
        .totals { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .totals td { padding: 3px 0; }
        .totals .label { color: #555; }
        .totals .amount { text-align: right; font-weight: 600; }
        .totals .grand td {
            padding-top: 8px;
            border-top: 1px solid #111;
            font-size: 14px;
            font-weight: 700;
        }
        .unpaid {
            border: 2px solid #111;
            padding: 8px;
            margin: 10px 0;
            text-align: center;
            font-weight: 700;
            letter-spacing: 0.06em;
        }
        .toolbar {
            max-width: 72mm;
            margin: 0 auto 12px;
            display: flex;
            gap: 8px;
        }
        .toolbar button,
        .toolbar a {
            flex: 1;
            padding: 8px 10px;
            border: 1px solid #111;
            background: #fff;
            color: #111;
            font: inherit;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }
        .toolbar .primary {
            background: #111;
            color: #fff;
        }
        @media print {
            body { padding: 0; }
            .toolbar { display: none !important; }
            @page { margin: 4mm; size: 80mm auto; }
        }
    </style>
</head>
<body @if ($autoPrint) onload="window.print()" @endif>
    <div class="toolbar">
        <button type="button" class="primary" onclick="window.print()">Print</button>
        <a href="{{ route('admin.orders.show', $order) }}">Back</a>
    </div>

    <div class="receipt">
        <div class="center">
            <div class="title">{{ $store->store_name ?: config('shop.store_name') }}</div>
            @if (filled($store->contact_address))
                <div class="muted">{{ $store->contact_address }}</div>
            @endif
            @php($phones = $store->invoiceContactPhones())
            @if ($phones !== [])
                <div class="muted">{{ implode(' · ', $phones) }}</div>
            @endif
            <div class="muted">{{ $store->contact_email ?: config('shop.contact_email') }}</div>
        </div>

        <div class="divider"></div>

        <div class="center title">RECEIPT</div>

        @if ($order->payment_status !== \App\Enums\PaymentStatus::Paid)
            <div class="unpaid">UNPAID</div>
        @endif

        <div class="meta">
            <strong>Receipt #{{ $order->order_number }}</strong>
            <div>{{ $order->invoiceDate()->format('M j, Y g:i A') }}</div>
            <div>{{ $order->kitchenFulfillmentLabel() }} · {{ $order->kitchenSourceLabel() }}</div>
            @if ($order->diningTable)
                <div>Table {{ $order->diningTable->code }} · {{ $order->diningTable->name }}</div>
            @endif
            @if ($order->billing_full_name && $order->billing_full_name !== 'Walk-in Customer')
                <div>Customer: {{ $order->billing_full_name }}</div>
            @endif
            @if (filled($order->billing_phone))
                <div>{{ $order->billing_phone }}</div>
            @endif
        </div>

        <div class="divider"></div>

        <table class="items">
            @foreach ($order->items as $item)
                <tr>
                    <td class="qty">{{ $item->quantity }}×</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if ($item->optionLabel())
                            <div class="muted">{{ $item->optionLabel() }}</div>
                        @endif
                    </td>
                    <td class="price">{{ $currency }} {{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        <table class="totals">
            <tr>
                <td class="label">Subtotal</td>
                <td class="amount">{{ $currency }} {{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if ((float) $order->discount_amount > 0)
                <tr>
                    <td class="label">Discount{{ $order->coupon_code ? ' ('.$order->coupon_code.')' : '' }}</td>
                    <td class="amount">- {{ $currency }} {{ number_format($order->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if ((float) $order->delivery_fee > 0 || (float) $order->shipping_fee > 0)
                <tr>
                    <td class="label">Delivery</td>
                    <td class="amount">{{ $currency }} {{ number_format((float) $order->delivery_fee + (float) $order->shipping_fee, 2) }}</td>
                </tr>
            @endif
            @if ((float) $order->tax > 0)
                <tr>
                    <td class="label">Tax</td>
                    <td class="amount">{{ $currency }} {{ number_format($order->tax, 2) }}</td>
                </tr>
            @endif
            <tr class="grand">
                <td>TOTAL</td>
                <td class="amount">{{ $currency }} {{ number_format($order->total, 2) }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="meta">
            <div><strong>Payment:</strong> {{ $order->receiptPaymentMethodLabel() }}</div>
            <div><strong>Status:</strong> {{ $order->payment_status->label() }}</div>
            @if ($order->payment?->paystackReference())
                <div class="muted">Ref: {{ $order->payment->paystackReference() }}</div>
            @endif
        </div>

        @if ($order->customer_comment)
            <div class="divider"></div>
            <div class="meta">
                <strong>Note</strong>
                <div>{{ $order->customer_comment }}</div>
            </div>
        @endif

        <div class="divider"></div>
        <div class="center muted">Thank you for dining with us!</div>
    </div>
</body>
</html>
