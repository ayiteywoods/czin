<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\DeletesBulkRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModifierGroupRequest;
use App\Models\Modifier;
use App\Models\ModifierGroup;
use App\Models\Product;
use App\Support\AdminTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ModifierGroupController extends Controller
{
    use DeletesBulkRecords;

    public function index(Request $request): View
    {
        $groups = AdminTable::paginate(
            ModifierGroup::query()->withCount('modifiers'),
            $request,
            [
                'name' => 'name',
                'min_selections' => 'min_selections',
                'max_selections' => 'max_selections',
                'is_active' => 'is_active',
                'created_at' => 'created_at',
            ],
            'name',
            'asc',
        );

        return view('admin.modifiers.index', compact('groups'));
    }

    public function create(): View
    {
        $products = Product::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.modifiers.create', compact('products'));
    }

    public function store(ModifierGroupRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $group = ModifierGroup::query()->create([
                'name' => $data['name'],
                'min_selections' => $data['min_selections'] ?? 0,
                'max_selections' => $data['max_selections'] ?? 1,
                'is_required' => $data['is_required'] ?? false,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $group->products()->sync($data['product_ids'] ?? []);
            $this->syncModifiers($group, $data['modifiers'] ?? []);
        });

        return redirect()
            ->route('admin.modifiers.index')
            ->with('success', 'Modifier group created successfully.');
    }

    public function edit(ModifierGroup $modifier_group): View
    {
        $modifier_group->load(['modifiers', 'products']);
        $products = Product::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.modifiers.edit', [
            'group' => $modifier_group,
            'products' => $products,
        ]);
    }

    public function update(ModifierGroupRequest $request, ModifierGroup $modifier_group): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($modifier_group, $data) {
            $modifier_group->update([
                'name' => $data['name'],
                'min_selections' => $data['min_selections'] ?? 0,
                'max_selections' => $data['max_selections'] ?? 1,
                'is_required' => $data['is_required'] ?? false,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $modifier_group->products()->sync($data['product_ids'] ?? []);
            $this->syncModifiers($modifier_group, $data['modifiers'] ?? []);
        });

        return redirect()
            ->route('admin.modifiers.index')
            ->with('success', 'Modifier group updated successfully.');
    }

    public function destroy(ModifierGroup $modifier_group): RedirectResponse
    {
        $modifier_group->delete();

        return redirect()
            ->route('admin.modifiers.index')
            ->with('success', 'Modifier group deleted successfully.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        return $this->destroySelected($request, ModifierGroup::class, 'modifier group');
    }

    /**
     * @param  list<array<string, mixed>>  $modifiers
     */
    private function syncModifiers(ModifierGroup $group, array $modifiers): void
    {
        $keptIds = [];

        foreach (array_values($modifiers) as $index => $row) {
            if (! filled($row['name'] ?? null)) {
                continue;
            }

            $modifier = isset($row['id'])
                ? $group->modifiers()->whereKey($row['id'])->first()
                : new Modifier(['modifier_group_id' => $group->id]);

            if (! $modifier) {
                continue;
            }

            $modifier->fill([
                'name' => $row['name'],
                'price' => $row['price'] ?? 0,
                'is_active' => (bool) ($row['is_active'] ?? true),
                'sort_order' => $row['sort_order'] ?? $index,
            ])->save();

            $keptIds[] = $modifier->id;
        }

        $group->modifiers()->whereNotIn('id', $keptIds)->delete();
    }
}
