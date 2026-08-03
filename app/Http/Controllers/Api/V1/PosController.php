<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PosOrderRequest;
use App\Models\Order;
use App\Services\Api\PosBootstrapService;
use App\Services\Api\ReceiptPayloadService;
use App\Services\PosOrderService;
use Illuminate\Http\JsonResponse;

class PosController extends Controller
{
    use RespondsWithJson;

    public function bootstrap(PosBootstrapService $bootstrap): JsonResponse
    {
        return $this->success($bootstrap->payload());
    }

    public function storeOrder(PosOrderRequest $request, PosOrderService $posOrders): JsonResponse
    {
        $order = $posOrders->createOrder($request->validated(), (int) auth()->id());

        return $this->success(
            app(ReceiptPayloadService::class)->forOrder($order),
            status: 201,
        );
    }

    public function showOrder(Order $order, ReceiptPayloadService $receipts): JsonResponse
    {
        return $this->success($receipts->forOrder($order));
    }
}
