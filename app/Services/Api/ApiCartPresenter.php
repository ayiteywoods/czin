<?php

namespace App\Services\Api;

use App\Models\Cart;
use App\Models\CartItem;
use App\Services\CheckoutService;

class ApiCartPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function present(Cart $cart, CheckoutService $checkout): array
    {
        $cart->load(['items.product.images', 'items.product.category', 'items.variant']);
        $items = $cart->items;
        $totals = $checkout->calculateTotals($items);

        return [
            'id' => $cart->id,
            'item_count' => $cart->itemCount(),
            'items' => $items->map(fn (CartItem $item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => $item->product?->name,
                'image_url' => $item->product?->storefrontImageUrl(),
                'variant_label' => $item->variant?->displayLabel(),
                'special_request' => $item->special_request,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->lineTotal(),
            ])->values()->all(),
            'totals' => $totals,
        ];
    }
}
