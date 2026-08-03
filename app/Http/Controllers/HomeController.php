<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\HomeContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const NEW_ARRIVALS_PER_PAGE = 8;

    public function __invoke(HomeContentService $content): View
    {
        $featuredProducts = $this->newArrivalsQuery()
            ->paginate(self::NEW_ARRIVALS_PER_PAGE);

        $categories = Category::query()
            ->parents()
            ->withCount('products')
            ->forShopDisplay()
            ->get();

        $menuCategories = $this->menuCategoriesWithDishes($categories);

        $sections = $content->sections();
        $testimonials = $content->testimonials();

        return view('storefront.home', compact(
            'featuredProducts',
            'categories',
            'menuCategories',
            'sections',
            'testimonials',
        ));
    }

    public function newArrivals(Request $request): JsonResponse
    {
        $products = $this->newArrivalsQuery()
            ->paginate(self::NEW_ARRIVALS_PER_PAGE, ['*'], 'page', max(1, $request->integer('page', 1)));

        return response()->json([
            'html' => view('storefront.partials.product-grid-items', [
                'products' => $products,
                'layout' => 'menu',
            ])->render(),
            'has_more' => $products->hasMorePages(),
            'next_page' => $products->hasMorePages() ? $products->currentPage() + 1 : null,
        ]);
    }

    /**
     * @param  Collection<int, Category>  $categories
     * @return Collection<int, Category>
     */
    private function menuCategoriesWithDishes(Collection $categories): Collection
    {
        $productsByCategory = Product::query()
            ->with([
                'category',
                'images',
                'variants' => fn ($query) => $query->where('is_active', true),
            ])
            ->visibleOnStorefront()
            ->latest()
            ->get()
            ->groupBy('category_id');

        return $categories->map(function (Category $category) use ($productsByCategory) {
            $dishes = collect($category->filterableProductCategoryIds())
                ->flatMap(fn (int $id) => $productsByCategory->get($id, collect()))
                ->unique('id')
                ->take(6)
                ->values();

            $category->setRelation('menuProducts', $dishes);

            return $category;
        })->filter(fn (Category $category) => $category->menuProducts->isNotEmpty())->values();
    }

    private function newArrivalsQuery()
    {
        return Product::query()
            ->with([
                'category',
                'images',
                'variants' => fn ($query) => $query->where('is_active', true),
            ])
            ->visibleOnStorefront()
            ->latest();
    }
}
