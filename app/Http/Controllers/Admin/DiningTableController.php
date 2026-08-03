<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiningTableRequest;
use App\Models\DiningTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DiningTableController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $area = $request->string('area')->trim()->toString();

        $tables = DiningTable::query()
            ->filtered($search ?: null, $status ?: null, $area ?: null)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->paginate(16)
            ->withQueryString();

        $stats = [
            'total' => DiningTable::query()->count(),
            'occupied' => DiningTable::query()->where('status', TableStatus::Occupied)->count(),
            'reserved' => DiningTable::query()->where('status', TableStatus::Reserved)->count(),
            'available' => DiningTable::query()->where('status', TableStatus::Available)->count(),
        ];

        $areas = DiningTable::query()
            ->select('area')
            ->distinct()
            ->orderBy('area')
            ->pluck('area');

        return view('admin.tables.index', compact('tables', 'stats', 'areas', 'search', 'status', 'area'));
    }

    public function create(): View
    {
        return view('admin.tables.create');
    }

    public function store(DiningTableRequest $request): RedirectResponse
    {
        DiningTable::create($request->validated());

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Table created successfully.');
    }

    public function edit(DiningTable $table): View
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(DiningTableRequest $request, DiningTable $table): RedirectResponse
    {
        $table->update($request->validated());

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Table updated successfully.');
    }

    public function updateStatus(Request $request, DiningTable $table): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(TableStatus::class)],
        ]);

        $table->update($validated);

        return redirect()
            ->route('admin.tables.index', $request->only(['q', 'status', 'area', 'page']))
            ->with('success', $table->code.' marked as '.$table->status->label().'.');
    }

    public function destroy(DiningTable $table): RedirectResponse
    {
        $table->delete();

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Table deleted successfully.');
    }
}
