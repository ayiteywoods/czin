<?php

namespace App\Services;

use App\Enums\FulfillmentType;
use App\Enums\OrderSource;
use App\Enums\PaymentStatus;
use App\Enums\TableStatus;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\StoreSetting;
use Carbon\Carbon;

class AdminOperationsService
{
    public function __construct(
        private readonly KitchenOrderService $kitchen,
    ) {}

    /**
     * @return array{
     *     pos_revenue: float,
     *     online_revenue: float,
     *     pos_orders: int,
     *     online_orders: int,
     *     dine_in_count: int,
     *     takeaway_count: int,
     *     delivery_count: int,
     *     kitchen_queue: array{new: int, preparing: int, ready: int},
     *     table_stats: array{available: int, occupied: int, reserved: int}
     * }
     */
    public function restaurantMetrics(Carbon $from, Carbon $to): array
    {
        $paidQuery = Order::query()
            ->where('payment_status', PaymentStatus::Paid)
            ->whereBetween('paid_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        $posQuery = (clone $paidQuery)->where('order_source', OrderSource::Pos);
        $onlineQuery = (clone $paidQuery)->where('order_source', OrderSource::Online);

        $kitchenStats = $this->kitchen->boardPayload()['stats'];

        $tableCounts = DiningTable::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'pos_revenue' => (float) (clone $posQuery)->sum('total'),
            'online_revenue' => (float) (clone $onlineQuery)->sum('total'),
            'pos_orders' => (clone $posQuery)->count(),
            'online_orders' => (clone $onlineQuery)->count(),
            'dine_in_count' => (clone $paidQuery)->where('fulfillment_type', FulfillmentType::DineIn)->count(),
            'takeaway_count' => (clone $paidQuery)->where('fulfillment_type', FulfillmentType::Takeaway)->count(),
            'delivery_count' => (clone $paidQuery)->where('fulfillment_type', FulfillmentType::Delivery)->count(),
            'kitchen_queue' => [
                'new' => (int) ($kitchenStats['new'] ?? 0),
                'preparing' => (int) ($kitchenStats['preparing'] ?? 0),
                'ready' => (int) ($kitchenStats['ready'] ?? 0),
            ],
            'table_stats' => [
                'available' => (int) ($tableCounts[TableStatus::Available->value] ?? 0),
                'occupied' => (int) ($tableCounts[TableStatus::Occupied->value] ?? 0),
                'reserved' => (int) ($tableCounts[TableStatus::Reserved->value] ?? 0),
            ],
        ];
    }

    /**
     * @return array{
     *     revenue: float,
     *     orders: int,
     *     payments: array<string, array{count: int, revenue: float}>
     * }
     */
    public function posEndOfDaySummary(Carbon $date): array
    {
        $orders = Order::query()
            ->where('order_source', OrderSource::Pos)
            ->where('payment_status', PaymentStatus::Paid)
            ->whereDate('paid_at', $date->toDateString())
            ->get(['total', 'payment_method']);

        $payments = $orders
            ->groupBy(fn (Order $order) => $order->payment_method ?: 'unknown')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'revenue' => round((float) $group->sum('total'), 2),
            ])
            ->sortKeys()
            ->all();

        return [
            'revenue' => round((float) $orders->sum('total'), 2),
            'orders' => $orders->count(),
            'payments' => $payments,
        ];
    }

    public function lowStockThreshold(): int
    {
        return StoreSetting::current()->lowStockThreshold();
    }
}
