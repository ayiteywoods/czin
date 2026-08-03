@extends('layouts.admin')

@section('heading', 'Reservations')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.reservations.create') }}" class="btn-primary w-full text-center sm:w-auto">Add reservation</a>
    </div>

    <x-admin-table-panel :page-ids="$reservations->pluck('id')">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <x-admin-sort-th column="guest_name" label="Guest" class="admin-cell-primary" />
                    <th class="admin-table-cell admin-col-md font-medium">Table</th>
                    <x-admin-sort-th column="party_size" label="Party" class="admin-col-md" />
                    <x-admin-sort-th column="reserved_at" label="When" class="admin-col-md" />
                    <x-admin-sort-th column="status" label="Status" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-actions text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservations as $reservation)
                    <tr>
                        <x-admin-table-leading-cells :id="$reservation->id" :number="$reservations->firstItem() + $loop->index" />
                        <td class="admin-table-cell admin-cell-primary">
                            <div class="font-medium">{{ $reservation->guest_name }}</div>
                            <div class="text-xs text-brand-muted">{{ $reservation->guest_phone ?? $reservation->guest_email ?? '—' }}</div>
                        </td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $reservation->diningTable?->code ?? '—' }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $reservation->party_size }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $reservation->reserved_at?->format('M j, Y g:i A') }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $reservation->status->label() }}</td>
                        <td class="admin-table-cell admin-col-actions">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.reservations.edit', $reservation) }}" class="btn-outline px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}" onsubmit="return confirm('Delete this reservation?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-outline px-3 py-1.5 text-xs text-brand-red">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="admin-table-cell py-8 text-center text-brand-muted">No reservations yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$reservations" />
@endsection
