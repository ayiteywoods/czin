<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DetailController extends Controller
{
    public function product(Product $product): JsonResponse
    {
        $product->load(['category', 'images']);

        return response()->json([
            'title' => $product->name,
            'html' => view('admin.details.product', compact('product'))->render(),
        ]);
    }

    public function category(Category $category): JsonResponse
    {
        $category->load(['parent', 'children'])->loadCount(['products', 'children']);

        return response()->json([
            'title' => $category->name,
            'html' => view('admin.details.category', compact('category'))->render(),
        ]);
    }

    public function order(Order $order): JsonResponse
    {
        $order->load(['user', 'items', 'payment']);

        return response()->json([
            'title' => 'Order '.$order->order_number,
            'html' => view('admin.details.order', compact('order'))->render(),
        ]);
    }

    public function customer(User $user): JsonResponse
    {
        abort_unless($user->role === UserRole::Customer, 404);

        $user->load(['customerTags', 'loyaltyAccount'])->loadCount('orders');

        $paidOrders = Order::query()
            ->where('user_id', $user->id)
            ->where('payment_status', PaymentStatus::Paid);

        $totalSpend = (float) (clone $paidOrders)->sum('total');
        $paidCount = (clone $paidOrders)->count();
        $averageOrder = $paidCount > 0 ? round($totalSpend / $paidCount, 2) : 0.0;
        $lastOrderDate = Order::query()
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->value('created_at');

        $recentOrders = Order::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'title' => $user->name,
            'html' => view('admin.details.customer', compact(
                'user',
                'recentOrders',
                'totalSpend',
                'averageOrder',
                'lastOrderDate',
            ))->render(),
        ]);
    }
}
