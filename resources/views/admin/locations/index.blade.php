@extends('layouts.admin')

@section('heading', 'Locations')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.locations.create') }}" class="btn-primary w-full text-center sm:w-auto">Add location</a>
    </div>

    <x-admin-table-panel :page-ids="$locations->pluck('id')">
        <x-slot:bulkActions>
            <x-admin-bulk-delete
                :action="route('admin.locations.bulk-destroy')"
                confirm="Delete the selected locations?"
                label="Delete selected"
            />
        </x-slot:bulkActions>
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <x-admin-sort-th column="name" label="Name" class="admin-cell-primary" />
                    <th class="admin-table-cell admin-col-md font-medium">Phone</th>
                    <x-admin-sort-th column="is_default" label="Default" class="admin-col-md" />
                    <x-admin-sort-th column="is_active" label="Status" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-actions text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($locations as $location)
                    <tr>
                        <x-admin-table-leading-cells :id="$location->id" :number="$locations->firstItem() + $loop->index" />
                        <td class="admin-table-cell admin-cell-primary">
                            <div class="font-medium">{{ $location->name }}</div>
                            <div class="text-xs text-brand-muted">{{ $location->address ?? '—' }}</div>
                        </td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $location->phone ?? '—' }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $location->is_default ? 'Yes' : 'No' }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $location->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="admin-table-cell admin-col-actions">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.locations.edit', $location) }}" class="btn-outline px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" onsubmit="return confirm('Delete this location?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-outline px-3 py-1.5 text-xs text-brand-red">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="admin-table-cell py-8 text-center text-brand-muted">No locations yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$locations" />
@endsection
