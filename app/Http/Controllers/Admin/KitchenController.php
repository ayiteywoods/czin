<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KitchenOrderStatusRequest;
use App\Models\Order;
use App\Services\KitchenOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(KitchenOrderService $kitchen): View
    {
        $board = $kitchen->boardPayload();

        return view('admin.kitchen.index', [
            'stats' => $board['stats'],
            'columns' => $board['columns'],
            'pollUrl' => route('admin.kitchen.poll'),
        ]);
    }

    public function poll(KitchenOrderService $kitchen): JsonResponse
    {
        return response()->json($kitchen->boardPayload());
    }

    public function print(Order $order): View
    {
        abort_unless($order->payment_status === \App\Enums\PaymentStatus::Paid, 404);

        $order->load(['items', 'diningTable']);

        return view('admin.kitchen.print', compact('order'));
    }

    public function updateStatus(
        KitchenOrderStatusRequest $request,
        Order $order,
        KitchenOrderService $kitchen,
    ): RedirectResponse|JsonResponse {
        $order = $kitchen->advanceStatus($order);

        $message = match ($order->status) {
            OrderStatus::Processing => 'Order moved to preparing.',
            OrderStatus::ReadyForDelivery => 'Order marked ready.',
            OrderStatus::Delivered => 'Order marked served and table released.',
            default => 'Kitchen status updated.',
        };

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'board' => $kitchen->boardPayload(),
            ]);
        }

        return back()->with('success', $message);
    }
}
