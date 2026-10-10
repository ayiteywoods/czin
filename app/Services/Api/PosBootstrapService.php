<?php

namespace App\Services\Api;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Enums\TableStatus;
use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Product;
use App\Models\StoreSetting;

class PosBootstrapService
{
    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $settings = StoreSetting::current();

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
            ->where('is_86ed', false)
            ->orderBy('name')
            ->get()
            ->filter(fn (Product $product) => $product->variants->isNotEmpty())
            ->values();

        $tables = DiningTable::query()
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'area', 'capacity', 'status']);

        return [
            'store' => [
                'name' => $settings->store_name ?: config('shop.store_name'),
                'currency_symbol' => config('shop.currency_symbol'),
                'tax_rate' => \App\Support\ShopTax::rate(),
                'tax_label' => \App\Support\ShopTax::label(),
                'contact_phone' => $settings->contact_phone,
            ],
            'categories' => $categories->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ])->values()->all(),
            'products' => $products->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'category' => $product->category?->name,
                'image_url' => $product->storefrontImageUrl(),
                'price' => $product->sellingPrice(),
                'variants' => $product->variants->map(fn ($variant) => [
                    'id' => $variant->id,
                    'label' => $variant->displayLabel(),
                    'price' => $variant->sellingPrice(),
                    'stock' => $variant->availableQuantity(),
                ])->values()->all(),
            ])->values()->all(),
            'tables' => $tables->map(fn (DiningTable $table) => [
                'id' => $table->id,
                'code' => $table->code,
                'name' => $table->name,
                'area' => $table->area,
                'capacity' => $table->capacity,
                'status' => $table->status->value,
                'status_label' => $table->status->label(),
            ])->values()->all(),
            'fulfillment_types' => collect(\App\Enums\FulfillmentType::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ])->values()->all(),
            'payment_methods' => [
                ['value' => 'cash', 'label' => 'Cash'],
                ['value' => 'card', 'label' => 'Card'],
                ['value' => 'momo', 'label' => 'Mobile Money'],
            ],
            'table_statuses' => collect(TableStatus::cases())->map(fn ($status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ])->values()->all(),
        ];
    }
}
