<?php

namespace App\Services;

use App\Enums\FulfillmentType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TableStatus;
use App\Models\Order;
use App\Services\AdminNotificationService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class KitchenOrderService
{
    /**
     * @return Collection<int, Order>
     */
    public function activeOrders(): Collection
    {
        return Order::query()
            ->kitchenActive()
            ->with(['items.product.images', 'items.product.category', 'diningTable'])
            ->orderBy('created_at')
            ->get();
    }

    /**
     * @return array{
     *     stats: array{new: int, preparing: int, ready: int, total: int},
     *     columns: array<string, list<array<string, mixed>>>
     * }
     */
    public function boardPayload(): array
    {
        $orders = $this->activeOrders();

        $columns = [
            'new' => [],
            'preparing' => [],
            'ready' => [],
        ];

        foreach ($orders as $order) {
            $columns[$order->kitchenColumn()][] = $this->serializeOrder($order);
        }

        return [
            'stats' => [
                'new' => count($columns['new']),
                'preparing' => count($columns['preparing']),
                'ready' => count($columns['ready']),
                'total' => $orders->count(),
            ],
            'columns' => $columns,
        ];
    }

    public function advanceStatus(Order $order): Order
    {
        if ($order->payment_status !== PaymentStatus::Paid) {
            throw ValidationException::withMessages([
                'status' => 'Only paid orders can be updated in the kitchen.',
            ]);
        }

        $action = $order->kitchenNextAction();

        if (! $action) {
            throw ValidationException::withMessages([
                'status' => 'This order cannot be advanced from the kitchen board.',
            ]);
        }

        $newStatus = $action['status'];

        if (! in_array($newStatus, $order->kitchenAllowedStatuses(), true)) {
            throw ValidationException::withMessages([
                'status' => 'That kitchen status change is not allowed.',
            ]);
        }

        $order->update(['status' => $newStatus]);

        $order->load('diningTable');

        if (
            $newStatus === OrderStatus::Delivered
            && $order->dining_table_id
            && $order->fulfillment_type === FulfillmentType::DineIn
        ) {
            $order->diningTable?->update(['status' => TableStatus::Available]);
        }

        app(AdminNotificationService::class)->sync();

        return $order->fresh(['items.product.images', 'items.product.category', 'diningTable']);
    }

    /**
     * @return array<string, mixed>
     */
    protected function serializeOrder(Order $order): array
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
            'update_url' => route('admin.kitchen.update-status', $order),
            'print_url' => route('admin.kitchen.print', $order),
        ];
    }
}
