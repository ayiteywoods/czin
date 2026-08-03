@extends('layouts.admin')

@section('heading', 'Loyalty account')
@section('subheading', ($loyalty->user?->name ?? 'Customer').' · '.$loyalty->points_balance.' points')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.loyalty.index') }}" class="text-sm text-brand-red hover:underline">← Back to loyalty</a>
    </div>

    <form method="POST" action="{{ route('admin.loyalty.adjust', $loyalty) }}" class="card mb-8 max-w-xl space-y-4 p-6">
        @csrf
        <h2 class="font-semibold">Adjust points</h2>
        <div>
            <x-form-label :required="true">Points (+/-)</x-form-label>
            <input type="number" name="points" value="{{ old('points') }}" required class="input-field" placeholder="e.g. 50 or -10">
            @error('points')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Note</label>
            <input type="text" name="note" value="{{ old('note') }}" class="input-field">
            @error('note')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary">Apply adjustment</button>
    </form>

    <x-admin-table-panel :page-ids="$transactions->pluck('id')">
        <div class="border-b border-neutral-200 px-4 py-4 sm:px-6">
            <h2 class="font-semibold">Transactions</h2>
        </div>
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <th class="admin-table-cell font-medium">Type</th>
                    <th class="admin-table-cell font-medium">Points</th>
                    <th class="admin-table-cell font-medium">Note / Order</th>
                    <th class="admin-table-cell font-medium">When</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $tx)
                    <tr>
                        <x-admin-table-leading-cells :id="$tx->id" :number="$transactions->firstItem() + $loop->index" />
                        <td class="admin-table-cell whitespace-nowrap">{{ $tx->type->label() }}</td>
                        <td class="admin-table-cell whitespace-nowrap">{{ $tx->points > 0 ? '+'.$tx->points : $tx->points }}</td>
                        <td class="admin-table-cell">{{ $tx->note ?? $tx->order?->order_number ?? '—' }}</td>
                        <td class="admin-table-cell whitespace-nowrap">{{ $tx->created_at?->format('M j, Y g:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="admin-table-cell py-8 text-center text-brand-muted">No transactions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$transactions" />
@endsection
