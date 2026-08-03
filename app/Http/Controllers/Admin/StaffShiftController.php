<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaffShiftRequest;
use App\Models\Location;
use App\Models\StaffShift;
use App\Models\User;
use App\Support\AdminTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffShiftController extends Controller
{
    public function index(Request $request): View
    {
        $shifts = AdminTable::paginate(
            StaffShift::query()->with(['user', 'location']),
            $request,
            [
                'starts_at' => 'starts_at',
                'ends_at' => 'ends_at',
                'role_label' => 'role_label',
                'created_at' => 'created_at',
            ],
            'starts_at',
            'desc',
        );

        return view('admin.staff-shifts.index', compact('shifts'));
    }

    public function create(): View
    {
        return view('admin.staff-shifts.create', $this->formData());
    }

    public function store(StaffShiftRequest $request): RedirectResponse
    {
        StaffShift::query()->create($request->validated());

        return redirect()
            ->route('admin.staff-shifts.index')
            ->with('success', 'Staff shift created successfully.');
    }

    public function edit(StaffShift $staff_shift): View
    {
        return view('admin.staff-shifts.edit', array_merge(
            ['shift' => $staff_shift],
            $this->formData(),
        ));
    }

    public function update(StaffShiftRequest $request, StaffShift $staff_shift): RedirectResponse
    {
        $staff_shift->update($request->validated());

        return redirect()
            ->route('admin.staff-shifts.index')
            ->with('success', 'Staff shift updated successfully.');
    }

    public function destroy(StaffShift $staff_shift): RedirectResponse
    {
        $staff_shift->delete();

        return redirect()
            ->route('admin.staff-shifts.index')
            ->with('success', 'Staff shift deleted successfully.');
    }

    /**
     * @return array{users: \Illuminate\Support\Collection, locations: \Illuminate\Support\Collection}
     */
    private function formData(): array
    {
        return [
            'users' => User::query()
                ->where('role', UserRole::Admin)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'locations' => Location::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
