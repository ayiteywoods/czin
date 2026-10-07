<?php

namespace App\Services;

use App\Enums\FulfillmentType;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PosReadyOrderService
{
    /**
     * Orders ready for POS staff to serve / hand over.
     *
     * @return Collection<int, Order>
     */
    public function readyOrders(): Collection
    {
        return Order::query()
            ->where('payment_status', PaymentStatus::Paid)
            ->where('status', OrderStatus::ReadyForDelivery)
            ->where(function ($query) {
                $query
                    ->where('order_source', OrderSource::Pos)
                    ->orWhereIn('fulfillment_type', [
                        FulfillmentType::DineIn,
                        FulfillmentType::Takeaway,
                    ]);
            })
            ->with(['items', 'diningTable', 'createdBy'])
            ->orderBy('updated_at')
            ->get();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function readyPayload(): array
    {
        return $this->readyOrders()
            ->map(fn (Order $order) => $this->serialize($order))
            ->values()
            ->all();
    }

    public function markServed(Order $order): Order
    {
        if ($order->payment_status !== PaymentStatus::Paid) {
            throw ValidationException::withMessages([
                'status' => 'Only paid orders can be marked served.',
            ]);
        }

        if ($order->status !== OrderStatus::ReadyForDelivery) {
            throw ValidationException::withMessages([
                'status' => 'Only ready orders can be marked served from POS.',
            ]);
        }

        if (! $order->canCompleteFromKitchen()) {
            throw ValidationException::withMessages([
                'status' => 'This order cannot be completed from POS.',
            ]);
        }

        return app(KitchenOrderService::class)->advanceStatus($order);
    }

    /**
     * @return array<string, mixed>
     */
    protected function serialize(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'fulfillment_label' => $order->kitchenFulfillmentLabel(),
            'source_label' => $order->kitchenSourceLabel(),
            'customer_name' => $order->billing_full_name,
            'cashier_name' => $order->createdBy?->name,
            'ready_for' => $order->updated_at?->diffForHumans(short: true),
            'table' => $order->diningTable ? [
                'code' => $order->diningTable->code,
                'name' => $order->diningTable->name,
                'area' => $order->diningTable->area,
            ] : null,
            'items' => $order->items->map(fn ($item) => [
                'name' => $item->product_name,
                'options' => $item->optionLabel(),
                'quantity' => $item->quantity,
            ])->values()->all(),
            'served_url' => route('admin.pos.mark-served', $order),
            'show_url' => route('admin.orders.show', $order),
        ];
    }
}
