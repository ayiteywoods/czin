<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\DeletesBulkRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromotionRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Support\AdminTable;
use App\Support\PromotionSchema;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PromotionController extends Controller
{
    use DeletesBulkRecords;

    public function index(Request $request): View
    {
        PromotionSchema::ensureMultiTargetColumns();

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
        PromotionSchema::ensureMultiTargetColumns();

        return view('admin.promotions.create', $this->formData());
    }

    public function store(PromotionRequest $request): RedirectResponse
    {
        if (! PromotionSchema::ensureMultiTargetColumns()) {
            return back()
                ->withInput()
                ->with('error', 'Could not prepare promotion fields. On the server run: php artisan migrate --force');
        }

        try {
            Promotion::query()->create($this->payload($request));
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Could not save promotion. Run php artisan migrate --force on the server, then try again.');
        }

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promotion created successfully.');
    }

    public function edit(Promotion $promotion): View
    {
        PromotionSchema::ensureMultiTargetColumns();
        $promotion->refresh();

        return view('admin.promotions.edit', array_merge(
            ['promotion' => $promotion],
            $this->formData(),
        ));
    }

    public function update(PromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        if (! PromotionSchema::ensureMultiTargetColumns()) {
            return back()
                ->withInput()
                ->with('error', 'Could not prepare promotion fields. On the server run: php artisan migrate --force');
        }

        try {
            $promotion->update($this->payload($request));
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Could not save promotion. Run php artisan migrate --force on the server, then try again.');
        }

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promotion updated successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(PromotionRequest $request): array
    {
        $data = $request->validated();
        $targets = Promotion::normalizeTargets(
            $data['category_ids'] ?? [],
            $data['product_ids'] ?? [],
        );

        return array_merge($data, $targets);
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promotion deleted successfully.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        return $this->destroySelected($request, Promotion::class, 'promotion');
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
