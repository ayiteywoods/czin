@extends('layouts.admin')

@section('heading', 'POS end of day')
@section('subheading', 'Daily POS sales summary')

@section('content')
    <form method="GET" action="{{ route('admin.pos-report.index') }}" class="card mb-6 flex flex-wrap items-end gap-4 p-4 sm:p-6">
        <div>
            <label for="date" class="block text-sm font-medium">Date</label>
            <input id="date" type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="input-field">
        </div>
        <button type="submit" class="btn-primary">View</button>
    </form>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-brand-muted">Revenue</p>
            <p class="mt-2 text-2xl font-semibold text-brand-red">GHS {{ number_format($summary['revenue'], 2) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-brand-muted">Orders</p>
            <p class="mt-2 text-2xl font-semibold">{{ $summary['orders'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-brand-muted">Avg order</p>
            <p class="mt-2 text-2xl font-semibold">
                GHS {{ number_format($summary['orders'] > 0 ? $summary['revenue'] / $summary['orders'] : 0, 2) }}
            </p>
        </div>
    </div>

    <div class="card mt-6 p-4 sm:p-6">
        <h2 class="mb-4 font-semibold">Payment breakdown</h2>
        @if (empty($summary['payments']))
            <p class="text-brand-muted">No POS payments for this date.</p>
        @else
            <table class="admin-data-table">
                <thead>
                    <tr>
                        <th class="admin-table-cell font-medium">Method</th>
                        <th class="admin-table-cell font-medium">Orders</th>
                        <th class="admin-table-cell font-medium">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($summary['payments'] as $method => $row)
                        <tr>
                            <td class="admin-table-cell capitalize">{{ $method }}</td>
                            <td class="admin-table-cell">{{ $row['count'] }}</td>
                            <td class="admin-table-cell">GHS {{ number_format($row['revenue'] ?? $row['total'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
