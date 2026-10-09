@extends('layouts.admin')

@section('heading', 'Promotions')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.promotions.create') }}" class="btn-primary w-full text-center sm:w-auto">Add promotion</a>
    </div>

    <x-admin-table-panel :page-ids="$promotions->pluck('id')">
        <x-slot:bulkActions>
            <x-admin-bulk-delete
                :action="route('admin.promotions.bulk-destroy')"
                confirm="Delete the selected promotions?"
                label="Delete selected"
            />
        </x-slot:bulkActions>
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <x-admin-sort-th column="name" label="Name" class="admin-cell-primary" />
                    <x-admin-sort-th column="type" label="Type" class="admin-col-md" />
                    <x-admin-sort-th column="value" label="Value" class="admin-col-md" />
                    <x-admin-sort-th column="starts_at" label="Starts" class="admin-col-md" />
                    <x-admin-sort-th column="is_active" label="Status" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-actions text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($promotions as $promotion)
                    <tr>
                        <x-admin-table-leading-cells :id="$promotion->id" :number="$promotions->firstItem() + $loop->index" />
                        <td class="admin-table-cell admin-cell-primary font-medium">{{ $promotion->name }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $promotion->type->label() }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $promotion->value }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $promotion->starts_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $promotion->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="admin-table-cell admin-col-actions">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.promotions.edit', $promotion) }}" class="btn-outline px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.promotions.destroy', $promotion) }}" onsubmit="return confirm('Delete this promotion?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-outline px-3 py-1.5 text-xs text-brand-red">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="admin-table-cell py-8 text-center text-brand-muted">No promotions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$promotions" />
@endsection
