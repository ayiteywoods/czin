@extends('layouts.admin')

@section('heading', 'Analytics')
@section('subheading', 'Restaurant segment performance')

@section('content')
    <form method="GET" action="{{ route('admin.analytics.index') }}" class="card mb-6 grid gap-4 p-4 sm:p-6 md:grid-cols-4 md:items-end">
        <div>
            <label for="from" class="block text-sm font-medium">From</label>
            <input id="from" type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="input-field">
        </div>
        <div>
            <label for="to" class="block text-sm font-medium">To</label>
            <input id="to" type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="input-field">
        </div>
        <div>
            <button type="submit" class="btn-primary w-full">Apply</button>
        </div>
    </form>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-brand-muted">Total revenue</p>
            <p class="mt-2 text-2xl font-semibold text-brand-red">GHS {{ number_format($summary['revenue'], 2) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-brand-muted">Orders</p>
            <p class="mt-2 text-2xl font-semibold">{{ $summary['orders'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-brand-muted">POS revenue</p>
            <p class="mt-2 text-2xl font-semibold">GHS {{ number_format($segment['pos_revenue'], 2) }}</p>
            <p class="mt-1 text-xs text-brand-muted">{{ $segment['pos_orders'] }} orders</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-brand-muted">Online revenue</p>
            <p class="mt-2 text-2xl font-semibold">GHS {{ number_format($segment['online_revenue'], 2) }}</p>
            <p class="mt-1 text-xs text-brand-muted">{{ $segment['online_orders'] }} orders</p>
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-brand-muted">Dine in</p>
            <p class="mt-2 text-2xl font-semibold">{{ $segment['dine_in_count'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-brand-muted">Takeaway</p>
            <p class="mt-2 text-2xl font-semibold">{{ $segment['takeaway_count'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-brand-muted">Delivery</p>
            <p class="mt-2 text-2xl font-semibold">{{ $segment['delivery_count'] }}</p>
        </div>
    </div>

    @php
        $channelTotal = max($segment['pos_revenue'] + $segment['online_revenue'], 0.01);
        $posPct = round(($segment['pos_revenue'] / $channelTotal) * 100);
        $onlinePct = 100 - $posPct;
        $fulfillmentTotal = max($segment['dine_in_count'] + $segment['takeaway_count'] + $segment['delivery_count'], 1);
    @endphp

    <div class="card mt-6 space-y-4 p-4 sm:p-6">
        <h2 class="font-semibold">Channel mix</h2>
        <div>
            <div class="mb-1 flex justify-between text-sm">
                <span>POS {{ $posPct }}%</span>
                <span>Online {{ $onlinePct }}%</span>
            </div>
            <div class="flex h-3 overflow-hidden rounded bg-neutral-200">
                <div class="bg-brand-red" style="width: {{ $posPct }}%"></div>
                <div class="bg-neutral-500" style="width: {{ $onlinePct }}%"></div>
            </div>
        </div>
        <div>
            <p class="mb-2 text-sm font-medium">Fulfillment mix</p>
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-3">
                    <span class="w-20 text-brand-muted">Dine in</span>
                    <div class="h-2 flex-1 rounded bg-neutral-200"><div class="h-2 rounded bg-brand-red" style="width: {{ round(($segment['dine_in_count'] / $fulfillmentTotal) * 100) }}%"></div></div>
                    <span class="w-8 text-right">{{ $segment['dine_in_count'] }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-20 text-brand-muted">Takeaway</span>
                    <div class="h-2 flex-1 rounded bg-neutral-200"><div class="h-2 rounded bg-neutral-600" style="width: {{ round(($segment['takeaway_count'] / $fulfillmentTotal) * 100) }}%"></div></div>
                    <span class="w-8 text-right">{{ $segment['takeaway_count'] }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-20 text-brand-muted">Delivery</span>
                    <div class="h-2 flex-1 rounded bg-neutral-200"><div class="h-2 rounded bg-amber-600" style="width: {{ round(($segment['delivery_count'] / $fulfillmentTotal) * 100) }}%"></div></div>
                    <span class="w-8 text-right">{{ $segment['delivery_count'] }}</span>
                </div>
            </div>
        </div>
        <p class="text-sm text-brand-muted">
            Growth:
            @if ($growth === null)
                —
            @else
                {{ $growth > 0 ? '+' : '' }}{{ $growth }}%
            @endif
        </p>
    </div>
@endsection
