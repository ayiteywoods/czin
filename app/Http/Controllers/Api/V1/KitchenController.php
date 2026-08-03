<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Api\KitchenBoardPresenter;
use App\Services\KitchenOrderService;
use Illuminate\Http\JsonResponse;

class KitchenController extends Controller
{
    use RespondsWithJson;

    public function board(KitchenOrderService $kitchen, KitchenBoardPresenter $presenter): JsonResponse
    {
        $orders = $kitchen->activeOrders();

        $columns = [
            'new' => [],
            'preparing' => [],
            'ready' => [],
        ];

        foreach ($orders as $order) {
            $columns[$order->kitchenColumn()][] = $presenter->serializeOrder($order);
        }

        return $this->success([
            'stats' => [
                'new' => count($columns['new']),
                'preparing' => count($columns['preparing']),
                'ready' => count($columns['ready']),
                'total' => $orders->count(),
            ],
            'columns' => $columns,
        ]);
    }

    public function advanceStatus(Order $order, KitchenOrderService $kitchen, KitchenBoardPresenter $presenter): JsonResponse
    {
        $order = $kitchen->advanceStatus($order)->load(['items', 'diningTable']);

        $orders = $kitchen->activeOrders();
        $columns = ['new' => [], 'preparing' => [], 'ready' => []];

        foreach ($orders as $activeOrder) {
            $columns[$activeOrder->kitchenColumn()][] = $presenter->serializeOrder($activeOrder);
        }

        return $this->success([
            'order' => $presenter->serializeOrder($order),
            'board' => [
                'stats' => [
                    'new' => count($columns['new']),
                    'preparing' => count($columns['preparing']),
                    'ready' => count($columns['ready']),
                    'total' => $orders->count(),
                ],
                'columns' => $columns,
            ],
        ]);
    }
}
