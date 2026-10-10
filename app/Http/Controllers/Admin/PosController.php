<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CategoryStatus;
use App\Enums\FulfillmentType;
use App\Enums\ProductStatus;
use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PosOrderRequest;
use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\Product;
use App\Services\PosOrderService;
use App\Services\PosReadyOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('status', CategoryStatus::Active)
            ->orderBy('shop_sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $products = Product::query()
            ->with([
                'category:id,name',
                'variants' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where('quantity', '>', 0)
                    ->orderBy('size')
                    ->orderBy('color'),
            ])
            ->where('status', ProductStatus::Active)
            ->orderBy('name')
            ->get()
            ->filter(fn (Product $product) => $product->variants->isNotEmpty())
            ->values();

        $tables = DiningTable::query()
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'area', 'capacity', 'status']);

        $promotions = app(\App\Services\PromotionService::class);

        $menuPayload = $products->map(function (Product $product) use ($promotions) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'category' => $product->category?->name,
                'image' => $product->storefrontImageUrl(),
                'price' => $product->sellingPrice(),
                'compare_at_price' => $product->compareAtPrice(),
                'promotion' => $promotions->promotionPayload($product),
                'variants' => $product->variants->map(fn ($variant) => [
                    'id' => $variant->id,
                    'label' => $variant->displayLabel(),
                    'price' => $variant->sellingPrice(),
                    'stock' => $variant->availableQuantity(),
                ])->values()->all(),
            ];
        })->values()->all();

        $readyPollUrl = null;
        $initialReadyOrders = [];

        try {
            $readyPollUrl = route('admin.pos.ready-orders');
            $initialReadyOrders = app(PosReadyOrderService::class)->readyPayload();
        } catch (\Throwable $e) {
            report($e);
        }

        return view('admin.pos.index', [
            'categories' => $categories,
            'menuPayload' => $menuPayload,
            'tables' => $tables,
            'fulfillmentTypes' => FulfillmentType::cases(),
            'tableStatuses' => collect(TableStatus::cases())->mapWithKeys(fn (TableStatus $status) => [
                $status->value => $status->label(),
            ]),
            'currencySymbol' => config('shop.currency_symbol'),
            'readyPollUrl' => $readyPollUrl,
            'initialReadyOrders' => $initialReadyOrders,
        ]);
    }

    public function store(PosOrderRequest $request, PosOrderService $posOrders): RedirectResponse
    {
        $order = $posOrders->createOrder($request->validated(), (int) auth()->id());

        return redirect()
            ->route('admin.orders.show', ['order' => $order, 'print_receipt' => 1])
            ->with('success', "POS order {$order->order_number} created and marked as paid.");
    }

    public function readyOrders(PosReadyOrderService $readyOrders): JsonResponse
    {
        $orders = $readyOrders->readyPayload();

        return response()->json([
            'orders' => $orders,
            'count' => count($orders),
        ]);
    }

    public function markServed(
        Request $request,
        Order $order,
        PosReadyOrderService $readyOrders,
    ): JsonResponse|RedirectResponse {
        $order = $readyOrders->markServed($order);
        $message = "Order {$order->order_number} marked as served.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'orders' => $readyOrders->readyPayload(),
            ]);
        }

        return back()->with('success', $message);
    }
}
