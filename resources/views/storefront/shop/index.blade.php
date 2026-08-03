@extends('layouts.storefront')

@section('title', 'Menu - CZIN')

@section('content')
    @include('storefront.partials.shop-hero')

    <div class="border-b border-neutral-200 bg-white/70" data-storefront-reveal>
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('shop.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative min-w-0 flex-1">
                    <svg class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-brand-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search dishes..."
                        class="input-field mt-0 w-full border-neutral-300 bg-brand-white py-2.5 pl-10 pr-3"
                    >
                </div>
                @if (request()->boolean('in_stock'))
                    <input type="hidden" name="in_stock" value="1">
                @endif
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <button type="submit" class="btn-primary px-6 py-2.5">Search</button>
            </form>
        </div>
    </div>

    {{-- Sticky category tabs --}}
    <div class="sticky top-[5.75rem] z-30 border-b border-neutral-200 bg-brand-white/95 backdrop-blur sm:top-[6.5rem]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-2 overflow-x-auto py-3" aria-label="Menu categories">
                <a
                    href="{{ route('shop.index', request()->except('category', 'page')) }}"
                    @class([
                        'menu-tab',
                        'menu-tab-active' => ! request()->filled('category'),
                    ])
                >
                    All
                </a>
                @foreach ($categoryTree as $category)
                    <a
                        href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $category->id])) }}"
                        @class([
                            'menu-tab',
                            'menu-tab-active' => (int) request('category') === $category->id,
                        ])
                    >
                        {{ $category->name }}
                    </a>
                    @foreach ($category->children as $child)
                        <a
                            href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $child->id])) }}"
                            @class([
                                'menu-tab',
                                'menu-tab-active' => (int) request('category') === $child->id,
                            ])
                        >
                            {{ $child->name }}
                        </a>
                    @endforeach
                @endforeach
            </nav>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-brand-muted">
                {{ $products->total() }} {{ \Illuminate\Support\Str::plural('dish', $products->total()) }}
                @if ($activeCategory)
                    in <span class="font-medium text-brand-black">{{ $activeCategory->name }}</span>
                @endif
            </p>
            <label class="inline-flex items-center gap-2 text-sm text-brand-muted">
                <input
                    type="checkbox"
                    class="text-brand-red focus:ring-brand-red"
                    @checked(request()->boolean('in_stock'))
                    onchange="window.location = this.checked
                        ? '{{ route('shop.index', array_merge(request()->except('page'), ['in_stock' => 1])) }}'
                        : '{{ route('shop.index', request()->except('page', 'in_stock')) }}'"
                >
                Available only
            </label>
        </div>

        <div id="shop-products-grid" class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-2">
            @forelse ($products as $product)
                @include('storefront.partials.menu-item-card', [
                    'product' => $product,
                    'revealDelay' => $loop->index * 80,
                ])
            @empty
                <div class="col-span-full w-full border border-dashed border-neutral-300 bg-white py-16 text-center">
                    <p class="text-lg font-medium text-brand-black">No dishes found</p>
                    <p class="mt-1 text-brand-muted">Try another category or search term.</p>
                    <a href="{{ route('shop.index') }}" class="btn-primary mt-6 inline-flex">Back to full menu</a>
                </div>
            @endforelse
        </div>

        @include('storefront.partials.load-more-button', [
            'hasMore' => $products->hasMorePages(),
            'url' => route('shop.index', request()->except('page')),
            'target' => '#shop-products-grid',
            'nextPage' => $products->currentPage() + 1,
        ])
    </div>
@endsection
