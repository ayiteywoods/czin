<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocationRequest;
use App\Models\Location;
use App\Support\AdminTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $locations = AdminTable::paginate(
            Location::query(),
            $request,
            [
                'name' => 'name',
                'is_active' => 'is_active',
                'is_default' => 'is_default',
                'created_at' => 'created_at',
            ],
            'name',
            'asc',
        );

        return view('admin.locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            if ($data['is_default'] ?? false) {
                Location::query()->update(['is_default' => false]);
            }

            Location::query()->create($data);
        });

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(LocationRequest $request, Location $location): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($location, $data) {
            if ($data['is_default'] ?? false) {
                Location::query()->whereKeyNot($location->id)->update(['is_default' => false]);
            }

            $location->update($data);
        });

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }
}
