@extends('layouts.admin')

@section('heading', 'Tables')
@section('subheading', 'Manage table configuration and view real-time status')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="grid flex-1 grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="admin-table-stat admin-table-stat-primary">
                <p class="admin-table-stat-label">Total tables</p>
                <p class="admin-table-stat-value">{{ $stats['total'] }}</p>
            </div>
            <div class="admin-table-stat">
                <p class="admin-table-stat-label">Occupied</p>
                <p class="admin-table-stat-value text-amber-600">{{ $stats['occupied'] }}</p>
            </div>
            <div class="admin-table-stat">
                <p class="admin-table-stat-label">Reserved</p>
                <p class="admin-table-stat-value text-blue-600">{{ $stats['reserved'] }}</p>
            </div>
            <div class="admin-table-stat">
                <p class="admin-table-stat-label">Available</p>
                <p class="admin-table-stat-value text-green-600">{{ $stats['available'] }}</p>
            </div>
        </div>

        <a href="{{ route('admin.tables.create') }}" class="btn-primary shrink-0">Add table</a>
    </div>

    <div class="card mb-6 p-4">
        <form method="GET" action="{{ route('admin.tables.index') }}" class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_180px_180px_auto]">
            <div class="relative">
                <svg class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-brand-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search tables..."
                    class="input-field mt-0 w-full py-2.5 pl-10"
                >
            </div>

            <select name="status" class="input-field mt-0 py-2.5">
                <option value="">All statuses</option>
                @foreach (\App\Enums\TableStatus::cases() as $statusOption)
                    <option value="{{ $statusOption->value }}" @selected($status === $statusOption->value)>
                        {{ $statusOption->label() }}
                    </option>
                @endforeach
            </select>

            <select name="area" class="input-field mt-0 py-2.5">
                <option value="">All areas</option>
                @foreach ($areas as $areaOption)
                    <option value="{{ $areaOption }}" @selected($area === $areaOption)>
                        {{ $areaOption }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary px-5 py-2.5">Filter</button>
                @if ($search || $status || $area)
                    <a href="{{ route('admin.tables.index') }}" class="btn-outline px-5 py-2.5">Reset</a>
                @endif
            </div>
        </form>
    </div>

    @if ($tables->isEmpty())
        <div class="card py-16 text-center">
            <p class="text-lg font-medium text-brand-black">No tables found</p>
            <p class="mt-1 text-sm text-brand-muted">Add your first dining table or adjust the filters.</p>
            <a href="{{ route('admin.tables.create') }}" class="btn-primary mt-6 inline-flex">Add table</a>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($tables as $table)
                @include('admin.tables.partials.card', ['table' => $table])
            @endforeach
        </div>

        <div class="mt-6">
            {{ $tables->links() }}
        </div>
    @endif
@endsection
