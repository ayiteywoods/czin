<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecipeRequest;
use App\Models\Product;
use App\Models\RecipeItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->withCount('recipeItems')
            ->with(['recipeItems' => fn ($q) => $q->orderBy('ingredient_name')])
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->string('q').'%');
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.recipes.index', compact('products'));
    }

    public function edit(Product $product): View
    {
        $product->load(['recipeItems' => fn ($q) => $q->orderBy('ingredient_name')]);

        return view('admin.recipes.edit', compact('product'));
    }

    public function update(RecipeRequest $request, Product $product): RedirectResponse
    {
        $items = $request->validated('items') ?? [];

        DB::transaction(function () use ($product, $items) {
            $product->recipeItems()->delete();

            foreach ($items as $item) {
                if (! filled($item['ingredient_name'] ?? null)) {
                    continue;
                }

                RecipeItem::query()->create([
                    'product_id' => $product->id,
                    'ingredient_name' => $item['ingredient_name'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? null,
                    'cost_per_unit' => $item['cost_per_unit'] ?? 0,
                ]);
            }
        });

        return redirect()
            ->route('admin.recipes.index')
            ->with('success', 'Recipe updated for '.$product->name.'.');
    }
}
