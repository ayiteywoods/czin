<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ShippingRegion;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    use RespondsWithJson;

    public function index(): JsonResponse
    {
        $settings = StoreSetting::current();

        $categories = Category::query()
            ->where('status', CategoryStatus::Active)
            ->orderBy('shop_sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description']);

        $products = Product::query()
            ->with([
                'category:id,name,slug',
                'images',
                'variants' => fn ($query) => $query->where('is_active', true)->orderBy('size')->orderBy('color'),
            ])
            ->visibleOnStorefront()
            ->not86ed()
            ->orderBy('name')
            ->get();

        $regions = ShippingRegion::query()
            ->with(['options' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $this->success([
            'store' => [
                'name' => $settings->store_name ?: config('shop.store_name'),
                'currency_symbol' => config('shop.currency_symbol'),
                'online_ordering_enabled' => $settings->isOnlineOrderingEnabled(),
                'maintenance_mode' => $settings->isMaintenanceModeEnabled(),
            ],
            'categories' => $categories->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ])->values()->all(),
            'products' => $products->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'category_id' => $product->category_id,
                'category' => $product->category?->only(['id', 'name', 'slug']),
                'image_url' => $product->storefrontImageUrl(),
                'price' => (float) $product->price,
                'discount_price' => $product->discount_price !== null ? (float) $product->discount_price : null,
                'selling_price' => $product->sellingPrice(),
                'compare_at_price' => $product->compareAtPrice(),
                'promotion' => app(\App\Services\PromotionService::class)->promotionPayload($product),
                'variants' => $product->variants
                    ->filter(fn ($variant) => $variant->availableQuantity() > 0)
                    ->map(fn ($variant) => [
                        'id' => $variant->id,
                        'label' => $variant->displayLabel(),
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'price' => $variant->sellingPrice(),
                        'stock' => $variant->availableQuantity(),
                    ])->values()->all(),
            ])->filter(fn (array $product) => $product['variants'] !== [])->values()->all(),
            'shipping_regions' => $regions->map(fn (ShippingRegion $region) => [
                'id' => $region->id,
                'name' => $region->name,
                'is_accra' => (bool) $region->is_accra,
                'options' => $region->options->map(fn ($option) => [
                    'id' => $option->id,
                    'name' => $option->name,
                    'price' => (float) $option->price,
                ])->values()->all(),
            ])->values()->all(),
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        abort_unless($product->isVisibleOnStorefront() && ! $product->is_86ed, 404);

        $product->load(['category', 'images', 'variants' => fn ($q) => $q->where('is_active', true)]);

        return $this->success([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'image_url' => $product->storefrontImageUrl(),
            'images' => $product->images->map(fn ($image) => $image->url())->values()->all(),
            'price' => $product->sellingPrice(),
            'variants' => $product->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'label' => $variant->displayLabel(),
                'size' => $variant->size,
                'color' => $variant->color,
                'price' => $variant->sellingPrice(),
                'stock' => $variant->availableQuantity(),
            ])->values()->all(),
        ]);
    }
}
