@extends('layouts.admin')

@section('heading', 'Loyalty')

@section('content')
    <x-admin-table-panel :page-ids="$accounts->pluck('id')">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <th class="admin-table-cell admin-cell-primary font-medium">Customer</th>
                    <x-admin-sort-th column="points_balance" label="Points" class="admin-col-md" />
                    <x-admin-sort-th column="updated_at" label="Updated" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-actions text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts as $account)
                    <tr>
                        <x-admin-table-leading-cells :id="$account->id" :number="$accounts->firstItem() + $loop->index" />
                        <td class="admin-table-cell admin-cell-primary">
                            <div class="font-medium">{{ $account->user?->name ?? '—' }}</div>
                            <div class="text-xs text-brand-muted">{{ $account->user?->email }}</div>
                        </td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $account->points_balance }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $account->updated_at?->format('M j, Y') }}</td>
                        <td class="admin-table-cell admin-col-actions text-right">
                            <a href="{{ route('admin.loyalty.show', $account) }}" class="btn-outline px-3 py-1.5 text-xs">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="admin-table-cell py-8 text-center text-brand-muted">No loyalty accounts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$accounts" />
@endsection
