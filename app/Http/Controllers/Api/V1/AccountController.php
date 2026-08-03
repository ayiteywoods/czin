<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Api\ReceiptPayloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    use RespondsWithJson;

    public function orders(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->success(
            $orders->getCollection()->map(fn (Order $order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total' => (float) $order->total,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'payment_status' => $order->payment_status->value,
                'payment_status_label' => $order->payment_status->label(),
                'created_at' => $order->created_at?->toIso8601String(),
                'paid_at' => $order->paid_at?->toIso8601String(),
            ])->values()->all(),
            [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        );
    }

    public function showOrder(Order $order, ReceiptPayloadService $receipts): JsonResponse
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 404);

        return $this->success([
            'order' => $receipts->forOrder($order),
            'tracking_steps' => $order->trackingSteps(),
        ]);
    }
}
