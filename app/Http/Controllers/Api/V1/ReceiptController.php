<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Api\ReceiptPayloadService;
use Illuminate\Http\JsonResponse;

class ReceiptController extends Controller
{
    use RespondsWithJson;

    public function show(Order $order, ReceiptPayloadService $receipts): JsonResponse
    {
        return $this->success($receipts->forOrder($order));
    }
}
