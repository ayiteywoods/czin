@extends('layouts.admin')

@section('heading', 'Staff shifts')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.staff-shifts.create') }}" class="btn-primary w-full text-center sm:w-auto">Add shift</a>
    </div>

    <x-admin-table-panel :page-ids="$shifts->pluck('id')">
        <x-slot:bulkActions>
            <x-admin-bulk-delete
                :action="route('admin.staff-shifts.bulk-destroy')"
                confirm="Delete the selected staff shifts?"
                label="Delete selected"
            />
        </x-slot:bulkActions>
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <th class="admin-table-cell admin-cell-primary font-medium">Staff</th>
                    <th class="admin-table-cell admin-col-md font-medium">Location</th>
                    <x-admin-sort-th column="starts_at" label="Starts" class="admin-col-md" />
                    <x-admin-sort-th column="ends_at" label="Ends" class="admin-col-md" />
                    <x-admin-sort-th column="role_label" label="Role" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-actions text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($shifts as $shift)
                    <tr>
                        <x-admin-table-leading-cells :id="$shift->id" :number="$shifts->firstItem() + $loop->index" />
                        <td class="admin-table-cell admin-cell-primary font-medium">{{ $shift->user?->name ?? '—' }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $shift->location?->name ?? '—' }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $shift->starts_at?->format('M j, g:i A') }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $shift->ends_at?->format('M j, g:i A') }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $shift->role_label ?? '—' }}</td>
                        <td class="admin-table-cell admin-col-actions">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.staff-shifts.edit', $shift) }}" class="btn-outline px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.staff-shifts.destroy', $shift) }}" onsubmit="return confirm('Delete this shift?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-outline px-3 py-1.5 text-xs text-brand-red">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="admin-table-cell py-8 text-center text-brand-muted">No staff shifts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$shifts" />
@endsection
