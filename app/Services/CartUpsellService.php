<?php

namespace App\Services;

use App\Enums\CategoryStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Support\Collection;

class CartUpsellService
{
    private const PER_CATEGORY = 2;

    private const MAX_TOTAL = 6;

    /**
     * @return Collection<int, Product>
     */
    public function suggestionsFor(Product $product): Collection
    {
        $slugs = StoreSetting::current()->upsellCategorySlugs();

        $categories = Category::query()
            ->whereIn('slug', $slugs)
            ->where('status', CategoryStatus::Active)
            ->get()
            ->keyBy('slug');

        $suggestions = collect();

        foreach ($slugs as $slug) {
            $category = $categories->get($slug);

            if (! $category) {
                continue;
            }

            $items = Product::query()
                ->with([
                    'category',
                    'images',
                    'variants' => fn ($query) => $query->where('is_active', true),
                ])
                ->visibleOnStorefront()
                ->where('category_id', $category->id)
                ->where('id', '!=', $product->id)
                ->orderBy('name')
                ->limit(self::PER_CATEGORY * 2)
                ->get()
                ->filter(fn (Product $candidate) => $candidate->defaultOrderVariant() !== null)
                ->take(self::PER_CATEGORY);

            $suggestions = $suggestions->merge($items);
        }

        return $suggestions->take(self::MAX_TOTAL)->values();
    }
}
