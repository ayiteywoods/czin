@extends('layouts.admin')

@section('heading', 'Modifiers')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.modifiers.create') }}" class="btn-primary w-full text-center sm:w-auto">Add modifier group</a>
    </div>

    <x-admin-table-panel :page-ids="$groups->pluck('id')">
        <x-slot:bulkActions>
            <x-admin-bulk-delete
                :action="route('admin.modifiers.bulk-destroy')"
                confirm="Delete the selected modifier groups?"
                label="Delete selected"
            />
        </x-slot:bulkActions>
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <x-admin-sort-th column="name" label="Group" class="admin-cell-primary" />
                    <th class="admin-table-cell admin-col-md font-medium">Modifiers</th>
                    <x-admin-sort-th column="min_selections" label="Min" class="admin-col-md" />
                    <x-admin-sort-th column="max_selections" label="Max" class="admin-col-md" />
                    <x-admin-sort-th column="is_active" label="Status" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-actions text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($groups as $group)
                    <tr>
                        <x-admin-table-leading-cells :id="$group->id" :number="$groups->firstItem() + $loop->index" />
                        <td class="admin-table-cell admin-cell-primary font-medium">{{ $group->name }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $group->modifiers_count }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $group->min_selections }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $group->max_selections }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $group->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="admin-table-cell admin-col-actions">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.modifiers.edit', $group) }}" class="btn-outline px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.modifiers.destroy', $group) }}" onsubmit="return confirm('Delete this modifier group?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-outline px-3 py-1.5 text-xs text-brand-red">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="admin-table-cell py-8 text-center text-brand-muted">No modifier groups yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$groups" />
@endsection
