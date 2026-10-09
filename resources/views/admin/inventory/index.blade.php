@extends('layouts.admin')

@section('heading', 'Inventory')
@section('subheading', 'Stock movement history')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.inventory.create') }}" class="btn-primary w-full text-center sm:w-auto">Adjust stock</a>
    </div>

    <x-admin-table-panel :page-ids="$movements->pluck('id')">
        <x-slot:bulkActions>
            <x-admin-bulk-delete
                :action="route('admin.inventory.bulk-destroy')"
                confirm="Delete the selected stock movement history records? This does not reverse stock levels."
                label="Delete selected"
            />
        </x-slot:bulkActions>
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <th class="admin-table-cell admin-cell-primary font-medium">Product</th>
                    <x-admin-sort-th column="type" label="Type" class="admin-col-md" />
                    <x-admin-sort-th column="quantity_change" label="Change" class="admin-col-md" />
                    <x-admin-sort-th column="quantity_after" label="After" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-md font-medium">By</th>
                    <x-admin-sort-th column="created_at" label="When" class="admin-col-md" />
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                    <tr>
                        <x-admin-table-leading-cells :id="$movement->id" :number="$movements->firstItem() + $loop->index" />
                        <td class="admin-table-cell admin-cell-primary">
                            <div class="font-medium">{{ $movement->product?->name ?? '—' }}</div>
                            @if ($movement->reason)
                                <div class="text-xs text-brand-muted">{{ $movement->reason }}</div>
                            @endif
                        </td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $movement->type->label() }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $movement->quantity_change > 0 ? '+'.$movement->quantity_change : $movement->quantity_change }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $movement->quantity_after }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $movement->user?->name ?? '—' }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $movement->created_at?->format('M j, Y g:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="admin-table-cell py-8 text-center text-brand-muted">No stock movements yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$movements" />
@endsection
