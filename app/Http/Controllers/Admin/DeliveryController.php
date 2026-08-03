<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DeliveryAssignmentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeliveryAssignRequest;
use App\Http\Requests\Admin\DeliveryStatusRequest;
use App\Models\DeliveryAssignment;
use App\Models\User;
use App\Support\AdminTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function index(Request $request): View
    {
        $assignments = AdminTable::paginate(
            DeliveryAssignment::query()->with(['order', 'driver']),
            $request,
            [
                'status' => 'status',
                'assigned_at' => 'assigned_at',
                'delivered_at' => 'delivered_at',
                'created_at' => 'created_at',
            ],
            'created_at',
            'desc',
        );

        $drivers = User::query()
            ->where('role', UserRole::Admin)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.delivery.index', compact('assignments', 'drivers'));
    }

    public function updateStatus(DeliveryStatusRequest $request, DeliveryAssignment $delivery): RedirectResponse
    {
        $status = DeliveryAssignmentStatus::from($request->validated('status'));

        $updates = [
            'status' => $status,
            'notes' => $request->validated('notes') ?? $delivery->notes,
        ];

        if ($status === DeliveryAssignmentStatus::Delivered) {
            $updates['delivered_at'] = now();
        }

        $delivery->update($updates);

        return back()->with('success', 'Delivery status updated.');
    }

    public function assign(DeliveryAssignRequest $request, DeliveryAssignment $delivery): RedirectResponse
    {
        $delivery->update([
            'driver_user_id' => $request->validated('driver_user_id'),
            'status' => DeliveryAssignmentStatus::Assigned,
            'assigned_at' => now(),
            'notes' => $request->validated('notes') ?? $delivery->notes,
        ]);

        return back()->with('success', 'Driver assigned.');
    }
}
