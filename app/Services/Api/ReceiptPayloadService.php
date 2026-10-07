<?php

namespace App\Services\Api;

use App\Models\Order;
use App\Models\StoreSetting;

class ReceiptPayloadService
{
    /**
     * @return array<string, mixed>
     */
    public function forOrder(Order $order): array
    {
        $order->loadMissing(['items', 'diningTable', 'payment', 'createdBy']);
        $store = StoreSetting::current();
        $currency = (string) config('shop.currency_symbol', 'GHS');

        return [
            'store' => [
                'name' => $store->store_name ?: config('shop.store_name'),
                'address' => $store->contact_address,
                'phones' => $store->invoiceContactPhones(),
                'email' => $store->contact_email,
            ],
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'created_at' => $order->created_at?->toIso8601String(),
                'paid_at' => $order->paid_at?->toIso8601String(),
                'fulfillment_type' => $order->fulfillment_type?->value,
                'fulfillment_label' => $order->kitchenFulfillmentLabel(),
                'source_label' => $order->kitchenSourceLabel(),
                'payment_status' => $order->payment_status->value,
                'payment_status_label' => $order->payment_status->label(),
                'payment_method' => $order->receiptPaymentMethodLabel(),
                'customer_name' => $order->billing_full_name,
                'customer_phone' => $order->billing_phone,
                'customer_comment' => $order->customer_comment,
                'cashier_name' => $order->createdBy?->name,
                'table' => $order->diningTable ? [
                    'code' => $order->diningTable->code,
                    'name' => $order->diningTable->name,
                    'area' => $order->diningTable->area,
                ] : null,
            ],
            'items' => $order->items->map(fn ($item) => [
                'name' => $item->product_name,
                'options' => $item->optionLabel(),
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
            ])->values()->all(),
            'totals' => [
                'subtotal' => (float) $order->subtotal,
                'discount' => (float) $order->discount_amount,
                'delivery' => (float) $order->delivery_fee + (float) $order->shipping_fee,
                'tax' => (float) $order->tax,
                'total' => (float) $order->total,
                'currency' => $currency,
            ],
            'paystack_reference' => $order->payment?->paystackReference(),
        ];
    }
}
