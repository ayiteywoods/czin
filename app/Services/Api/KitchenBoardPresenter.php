<?php

namespace App\Services\Api;

use App\Models\Order;

class KitchenBoardPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function serializeOrder(Order $order): array
    {
        $action = $order->kitchenNextAction();

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status->value,
            'status_label' => $order->kitchenStatusLabel(),
            'fulfillment_type' => $order->fulfillment_type?->value,
            'fulfillment_label' => $order->kitchenFulfillmentLabel(),
            'source_label' => $order->kitchenSourceLabel(),
            'table' => $order->diningTable ? [
                'code' => $order->diningTable->code,
                'name' => $order->diningTable->name,
                'area' => $order->diningTable->area,
            ] : null,
            'customer_name' => $order->billing_full_name,
            'customer_comment' => $order->customer_comment,
            'created_at' => $order->created_at?->diffForHumans(short: true),
            'created_at_iso' => $order->created_at?->toIso8601String(),
            'items' => $order->items->map(fn ($item) => [
                'name' => $item->product_name,
                'options' => $item->variant_options,
                'quantity' => $item->quantity,
                'image_url' => $item->product?->storefrontImageUrl(),
            ])->values()->all(),
            'next_action' => $action ? [
                'status' => $action['status']->value,
                'label' => $action['label'],
            ] : null,
        ];
    }
}
