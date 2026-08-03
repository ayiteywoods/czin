@php
    use App\Enums\DeliveryAssignmentStatus;
@endphp

@extends('layouts.admin')

@section('heading', 'Delivery')
@section('subheading', 'Driver assignments')

@section('content')
    <x-admin-table-panel :page-ids="$assignments->pluck('id')">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <th class="admin-table-cell admin-cell-primary font-medium">Order</th>
                    <th class="admin-table-cell admin-col-md font-medium">Driver</th>
                    <x-admin-sort-th column="status" label="Status" class="admin-col-md" />
                    <x-admin-sort-th column="assigned_at" label="Assigned" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-actions text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assignments as $assignment)
                    <tr>
                        <x-admin-table-leading-cells :id="$assignment->id" :number="$assignments->firstItem() + $loop->index" />
                        <td class="admin-table-cell admin-cell-primary">
                            @if ($assignment->order)
                                <a href="{{ route('admin.orders.show', $assignment->order) }}" class="font-medium text-brand-red hover:underline">
                                    {{ $assignment->order->order_number }}
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $assignment->driver?->name ?? 'Unassigned' }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $assignment->status->label() }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $assignment->assigned_at?->format('M j, g:i A') ?? '—' }}</td>
                        <td class="admin-table-cell admin-col-actions">
                            <div class="flex flex-col items-end gap-2">
                                <form method="POST" action="{{ route('admin.delivery.assign', $assignment) }}" class="flex flex-wrap items-center justify-end gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="driver_user_id" required class="input-field mt-0 w-40 py-1.5 text-sm">
                                        <option value="">Driver</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}" @selected($assignment->driver_user_id === $driver->id)>{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-outline px-3 py-1.5 text-xs">Assign</button>
                                </form>
                                <form method="POST" action="{{ route('admin.delivery.update-status', $assignment) }}" class="flex flex-wrap items-center justify-end gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="input-field mt-0 w-40 py-1.5 text-sm">
                                        @foreach (DeliveryAssignmentStatus::cases() as $status)
                                            <option value="{{ $status->value }}" @selected($assignment->status === $status)>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-outline px-3 py-1.5 text-xs">Update</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="admin-table-cell py-8 text-center text-brand-muted">No delivery assignments yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$assignments" />
@endsection
