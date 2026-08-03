<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kitchen Ticket #{{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #111;
            background: #fff;
        }
        .ticket { max-width: 72mm; margin: 0 auto; }
        .center { text-align: center; }
        .title { font-size: 16px; font-weight: 700; letter-spacing: 0.04em; }
        .muted { color: #555; }
        .divider { border-top: 1px dashed #999; margin: 10px 0; }
        .meta { margin: 8px 0; }
        .meta strong { display: block; }
        .items { width: 100%; border-collapse: collapse; }
        .items td { padding: 6px 0; vertical-align: top; }
        .qty { width: 28px; font-weight: 700; }
        .note {
            border: 1px solid #111;
            padding: 8px;
            margin-top: 10px;
            font-weight: 700;
        }
        @media print {
            body { padding: 0; }
            @page { margin: 4mm; size: 80mm auto; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="ticket">
        <div class="center">
            <div class="title">KITCHEN TICKET</div>
            <div class="muted">{{ config('shop.store_name') }}</div>
        </div>

        <div class="divider"></div>

        <div class="meta">
            <strong>Order #{{ $order->order_number }}</strong>
            <div>{{ $order->created_at?->format('M j, Y g:i A') }}</div>
            <div>{{ $order->kitchenFulfillmentLabel() }} · {{ $order->kitchenSourceLabel() }}</div>
            @if ($order->diningTable)
                <div>Table {{ $order->diningTable->code }} · {{ $order->diningTable->name }} ({{ $order->diningTable->area }})</div>
            @endif
            @if ($order->billing_full_name && $order->billing_full_name !== 'Walk-in Customer')
                <div>Customer: {{ $order->billing_full_name }}</div>
            @endif
        </div>

        <div class="divider"></div>

        <table class="items">
            @foreach ($order->items as $item)
                <tr>
                    <td class="qty">{{ $item->quantity }}×</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if (is_array($item->variant_options))
                            <div class="muted">
                                @foreach ($item->variant_options as $key => $value)
                                    @if (filled($value))
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}@if (! $loop->last), @endif
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        @if ($order->customer_comment)
            <div class="note">
                NOTE: {{ $order->customer_comment }}
            </div>
        @endif

        <div class="divider"></div>
        <div class="center muted">{{ $order->kitchenStatusLabel() }}</div>
    </div>
</body>
</html>
