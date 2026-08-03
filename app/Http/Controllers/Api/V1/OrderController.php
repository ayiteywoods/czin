<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\FulfillmentType;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrderStatusRequest;
use App\Models\Order;
use App\Services\Api\ReceiptPayloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $query = Order::query()->with('user')->latest();

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status')->toString());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('order_source')) {
            $query->where('order_source', $request->string('order_source')->toString());
        }

        if ($request->filled('fulfillment_type')) {
            $query->where('fulfillment_type', $request->string('fulfillment_type')->toString());
        }

        $orders = $query->paginate($request->integer('per_page', 20));

        return $this->success(
            $orders->getCollection()->map(fn (Order $order) => $this->summary($order))->values()->all(),
            [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        );
    }

    public function show(Order $order, ReceiptPayloadService $receipts): JsonResponse
    {
        return $this->success($receipts->forOrder($order));
    }

    public function updateStatus(OrderStatusRequest $request, Order $order): JsonResponse
    {
        $order->update(['status' => OrderStatus::from($request->validated('status'))]);

        return $this->success($this->summary($order->fresh(['user', 'diningTable'])));
    }

    /**
     * @return array<string, mixed>
     */
    protected function summary(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->user?->name ?? $order->billing_full_name,
            'total' => (float) $order->total,
            'payment_status' => $order->payment_status->value,
            'payment_status_label' => $order->payment_status->label(),
            'status' => $order->status->value,
            'status_label' => $order->status->label(),
            'order_source' => $order->order_source?->value ?? OrderSource::Online->value,
            'fulfillment_type' => $order->fulfillment_type?->value,
            'created_at' => $order->created_at?->toIso8601String(),
            'paid_at' => $order->paid_at?->toIso8601String(),
        ];
    }
}
