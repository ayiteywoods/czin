<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromotionRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Support\AdminTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(Request $request): View
    {
        $promotions = AdminTable::paginate(
            Promotion::query()->with(['category', 'product']),
            $request,
            [
                'name' => 'name',
                'type' => 'type',
                'value' => 'value',
                'is_active' => 'is_active',
                'starts_at' => 'starts_at',
                'ends_at' => 'ends_at',
                'created_at' => 'created_at',
            ],
            'created_at',
            'desc',
        );

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create(): View
    {
        return view('admin.promotions.create', $this->formData());
    }

    public function store(PromotionRequest $request): RedirectResponse
    {
        Promotion::query()->create($request->validated());

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promotion created successfully.');
    }

    public function edit(Promotion $promotion): View
    {
        return view('admin.promotions.edit', array_merge(
            ['promotion' => $promotion],
            $this->formData(),
        ));
    }

    public function update(PromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        $promotion->update($request->validated());

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promotion updated successfully.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promotion deleted successfully.');
    }

    /**
     * @return array{categories: \Illuminate\Support\Collection, products: \Illuminate\Support\Collection}
     */
    private function formData(): array
    {
        return [
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
