<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReservationRequest;
use App\Models\DiningTable;
use App\Models\Reservation;
use App\Support\AdminTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $reservations = AdminTable::paginate(
            Reservation::query()->with(['diningTable', 'user']),
            $request,
            [
                'guest_name' => 'guest_name',
                'party_size' => 'party_size',
                'reserved_at' => 'reserved_at',
                'status' => 'status',
                'created_at' => 'created_at',
            ],
            'reserved_at',
            'desc',
        );

        return view('admin.reservations.index', compact('reservations'));
    }

    public function create(): View
    {
        $tables = DiningTable::query()->orderBy('sort_order')->orderBy('code')->get();

        return view('admin.reservations.create', compact('tables'));
    }

    public function store(ReservationRequest $request): RedirectResponse
    {
        Reservation::query()->create($request->validated());

        return redirect()
            ->route('admin.reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function edit(Reservation $reservation): View
    {
        $tables = DiningTable::query()->orderBy('sort_order')->orderBy('code')->get();

        return view('admin.reservations.edit', compact('reservation', 'tables'));
    }

    public function update(ReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        $reservation->update($request->validated());

        return redirect()
            ->route('admin.reservations.index')
            ->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $reservation->delete();

        return redirect()
            ->route('admin.reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }
}
