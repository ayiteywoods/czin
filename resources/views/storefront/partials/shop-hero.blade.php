@php
    $heading = 'Our Menu';
    $eyebrow = 'Order online';
    $description = 'Fresh meals, sides, drinks, and desserts — made to order for delivery or pickup.';

    if (request()->filled('q')) {
        $heading = 'Results for “'.request('q').'”';
        $eyebrow = 'Search';
        $description = 'Dishes matching your search across the CZIN menu.';
    } elseif ($activeCategory) {
        $heading = $activeCategory->name;
        $eyebrow = 'Menu';
        $description = $activeCategory->description ?: $description;
    }
@endphp

<section class="border-b border-neutral-200 bg-brand-black text-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-brand-yellow">{{ $eyebrow }}</p>
        <div class="mt-3 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <h1 class="font-serif text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">{{ $heading }}</h1>
                <p class="mt-3 text-sm leading-relaxed text-neutral-300 sm:text-base">{{ $description }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-2 border border-white/15 bg-white/5 px-3 py-2 text-xs uppercase tracking-wide text-white/90">
                    <x-nav-icon icon="bag" class="h-3.5 w-3.5 text-brand-yellow" />
                    {{ $products->total() }} dishes
                </span>
                <span class="inline-flex items-center gap-2 border border-white/15 bg-white/5 px-3 py-2 text-xs uppercase tracking-wide text-white/90">
                    <x-nav-icon icon="truck" class="h-3.5 w-3.5 text-brand-yellow" />
                    Accra delivery
                </span>
            </div>
        </div>

        @if ($activeCategory || request()->filled('q') || request()->boolean('in_stock'))
            <div class="mt-6 flex flex-wrap gap-2">
                @if ($activeCategory)
                    <a href="{{ route('shop.index', request()->except('category', 'page')) }}" class="shop-hero-chip">
                        {{ $activeCategory->name }}
                        <span class="text-white/50">&times;</span>
                    </a>
                @endif
                @if (request()->filled('q'))
                    <a href="{{ route('shop.index', request()->except('q', 'page')) }}" class="shop-hero-chip">
                        “{{ request('q') }}”
                        <span class="text-white/50">&times;</span>
                    </a>
                @endif
                @if (request()->boolean('in_stock'))
                    <a href="{{ route('shop.index', request()->except('in_stock', 'page')) }}" class="shop-hero-chip">
                        Available only
                        <span class="text-white/50">&times;</span>
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
