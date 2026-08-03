@php
    use App\Models\HomeSection;

    $hero = $sections->get(HomeSection::KEY_HERO);
    $shopCategory = $sections->get(HomeSection::KEY_SHOP_CATEGORY);
    $cta = $sections->get(HomeSection::KEY_CTA);
    $newArrivals = $sections->get(HomeSection::KEY_NEW_ARRIVALS);
@endphp

@extends('layouts.storefront')

@section('title', 'CZIN — Fresh Meals Delivered')

@section('content')
    {{-- Restaurant hero with animated image carousel --}}
    @if ($hero?->is_active)
        @php
            $heroSlides = collect([
                asset('images/brand/food-hero-1.jpg'),
                asset('images/brand/food-hero-2.jpg'),
                asset('images/brand/food-hero-3.jpg'),
                asset('images/brand/food-hero-4.jpg'),
            ])->unique()->values();
        @endphp

        @php
            $heroTitleText = trim($hero->title ?? '');
            $heroHighlightText = trim($hero->title_highlight ?? '');
            $heroFullTitle = trim($heroTitleText.' '.$heroHighlightText);
        @endphp

        <section
            class="restaurant-hero"
            x-data="{
                active: 0,
                total: {{ $heroSlides->count() }},
                timer: null,
                titleText: @js($heroTitleText),
                highlightText: @js($heroHighlightText),
                titlePart: '',
                highlightPart: '',
                titleIndex: 0,
                highlightIndex: 0,
                typewriterPhase: 'title',
                typewriterDone: false,
                showCursor: false,
                reducedMotion: false,
                start() {
                    this.timer = setInterval(() => {
                        this.active = (this.active + 1) % this.total;
                    }, 4500);
                },
                stop() {
                    if (this.timer) clearInterval(this.timer);
                },
                go(index) {
                    this.active = index;
                    this.stop();
                    this.start();
                },
                startTypewriter() {
                    this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                    if (this.reducedMotion) {
                        this.titlePart = this.titleText;
                        this.highlightPart = this.highlightText ? ' '.$this.highlightText : '';
                        this.typewriterDone = true;
                        return;
                    }

                    this.showCursor = true;
                    setTimeout(() => this.typeNext(), 500);
                },
                resetTypewriter() {
                    this.titlePart = '';
                    this.highlightPart = '';
                    this.titleIndex = 0;
                    this.highlightIndex = 0;
                    this.typewriterPhase = 'title';
                },
                typeNext() {
                    if (this.typewriterPhase === 'title') {
                        if (this.titleIndex < this.titleText.length) {
                            this.titlePart += this.titleText[this.titleIndex];
                            this.titleIndex++;
                            setTimeout(() => this.typeNext(), 58);
                            return;
                        }

                        this.typewriterPhase = 'highlight';
                        if (this.titlePart && this.highlightText) {
                            this.highlightPart = ' ';
                        }
                        setTimeout(() => this.typeNext(), 220);
                        return;
                    }

                    if (this.typewriterPhase === 'highlight') {
                        if (this.highlightIndex < this.highlightText.length) {
                            this.highlightPart += this.highlightText[this.highlightIndex];
                            this.highlightIndex++;
                            setTimeout(() => this.typeNext(), 58);
                            return;
                        }

                        if (! this.typewriterDone) {
                            this.typewriterDone = true;
                        }

                        this.typewriterPhase = 'pause';
                        setTimeout(() => this.typeNext(), 2800);
                        return;
                    }

                    if (this.typewriterPhase === 'pause') {
                        this.typewriterPhase = 'delete';
                        setTimeout(() => this.typeNext(), 50);
                        return;
                    }

                    if (this.typewriterPhase === 'delete') {
                        if (this.highlightPart.length > 0) {
                            this.highlightPart = this.highlightPart.slice(0, -1);
                            setTimeout(() => this.typeNext(), 32);
                            return;
                        }

                        if (this.titlePart.length > 0) {
                            this.titlePart = this.titlePart.slice(0, -1);
                            setTimeout(() => this.typeNext(), 32);
                            return;
                        }

                        this.resetTypewriter();
                        setTimeout(() => this.typeNext(), 700);
                    }
                }
            }"
            x-init="start(); startTypewriter();"
            @mouseenter="stop()"
            @mouseleave="start()"
        >
            <div class="restaurant-hero-slides" aria-hidden="true">
                @foreach ($heroSlides as $index => $slide)
                    <div
                        class="restaurant-hero-slide"
                        :class="{ 'is-active': active === {{ $index }} }"
                        style="background-image: url('{{ $slide }}');"
                    ></div>
                @endforeach
            </div>

            <div class="relative z-10 mx-auto flex max-w-7xl flex-col gap-4 px-4 py-5 sm:gap-8 sm:px-6 sm:py-20 lg:flex-row lg:items-end lg:justify-between lg:px-8 lg:py-24">
                <div class="max-w-2xl">
                    @if ($hero->eyebrow)
                        <p class="restaurant-hero-animate restaurant-hero-animate-delay-1 inline-flex items-center gap-2 rounded-full bg-brand-yellow px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-black">
                            {{ $hero->eyebrow }}
                        </p>
                    @endif
                    <h1 class="hero-typewriter mt-4 font-serif text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                        <span class="sr-only">{{ $heroFullTitle }}</span>
                        <span aria-hidden="true">
                            <span x-text="titlePart"></span><span class="text-brand-yellow" x-text="highlightPart"></span><span class="hero-typewriter-cursor" x-show="showCursor" x-cloak>|</span>
                        </span>
                    </h1>
                    @if ($hero->body)
                        <p
                            x-show="typewriterDone"
                            x-cloak
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-y-3"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-4 hidden max-w-xl text-sm leading-relaxed text-white/85 sm:block sm:text-base"
                        >{{ $hero->body }}</p>
                    @endif
                    <div
                        x-show="typewriterDone"
                        x-cloak
                        x-transition:enter="transition ease-out duration-500 delay-100"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="mt-8 hidden flex-wrap gap-3 sm:flex"
                    >
                        @if ($hero->primary_label)
                            <a href="{{ $hero->resolvedUrl($hero->primary_url) }}" class="btn-primary px-7 py-3.5 transition-transform duration-300 hover:scale-[1.03]">{{ $hero->primary_label }}</a>
                        @endif
                        @if ($hero->secondary_label)
                            <a href="{{ $hero->resolvedUrl($hero->secondary_url) }}" class="hero-btn-outline px-7 py-3.5 transition-transform duration-300 hover:scale-[1.03]">{{ $hero->secondary_label }}</a>
                        @endif
                    </div>
                </div>

                <div class="hidden w-full max-w-md flex-col gap-4 sm:flex lg:w-auto">
                    <div class="grid grid-cols-3 gap-2 sm:gap-3">
                        <div class="restaurant-hero-stat restaurant-hero-animate restaurant-hero-animate-delay-4">
                            <p class="text-lg font-bold text-brand-yellow sm:text-xl">45–90</p>
                            <p class="mt-1 text-[10px] uppercase tracking-wide text-white/70">Min delivery</p>
                        </div>
                        <div class="restaurant-hero-stat restaurant-hero-animate restaurant-hero-animate-delay-5">
                            <p class="text-lg font-bold text-brand-yellow sm:text-xl">Fresh</p>
                            <p class="mt-1 text-[10px] uppercase tracking-wide text-white/70">Made to order</p>
                        </div>
                        <div class="restaurant-hero-stat restaurant-hero-animate restaurant-hero-animate-delay-6">
                            <p class="text-lg font-bold text-brand-yellow sm:text-xl">Accra</p>
                            <p class="mt-1 text-[10px] uppercase tracking-wide text-white/70">Hot delivery</p>
                        </div>
                    </div>

                    <div class="restaurant-hero-animate restaurant-hero-animate-delay-6 flex items-center justify-center gap-2 lg:justify-end" role="tablist" aria-label="Hero images">
                        @foreach ($heroSlides as $index => $slide)
                            <button
                                type="button"
                                class="restaurant-hero-dot"
                                :class="{ 'is-active': active === {{ $index }} }"
                                @click="go({{ $index }})"
                                :aria-selected="active === {{ $index }}"
                                aria-label="Show image {{ $index + 1 }}"
                            ></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Quick service strip --}}
    <section class="hidden border-b border-neutral-200 bg-brand-white sm:block">
        <div class="mx-auto grid max-w-7xl divide-y divide-neutral-200 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
            <div class="flex items-center gap-3 px-4 py-4 sm:px-6 lg:px-8" data-storefront-reveal style="--reveal-delay: 0ms">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-yellow text-brand-black">
                    <x-nav-icon icon="truck" class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide">Delivery</p>
                    <p class="text-xs text-brand-muted">Across Accra · pay rider on arrival</p>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-4 sm:px-6 lg:px-8" data-storefront-reveal style="--reveal-delay: 90ms">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-red text-white">
                    <x-nav-icon icon="bag" class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide">Pickup</p>
                    <p class="text-xs text-brand-muted">Order ahead · collect hot</p>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-4 sm:px-6 lg:px-8" data-storefront-reveal style="--reveal-delay: 180ms">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-black text-brand-yellow">
                    <x-nav-icon icon="sparkle" class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide">Kitchen open</p>
                    <p class="text-xs text-brand-muted">Order online anytime</p>
                </div>
            </div>
        </div>
    </section>

    @include('storefront.partials.free-delivery-banner', ['section' => $sections->get(HomeSection::KEY_FREE_DELIVERY)])

    {{-- Category chips --}}
    @if ($shopCategory?->is_active && $categories->isNotEmpty())
        <section class="hidden border-y border-neutral-200 bg-transparent py-10 sm:block sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-storefront-reveal>
                    <div>
                        @if ($shopCategory->eyebrow)
                            <p class="section-eyebrow">{{ $shopCategory->eyebrow }}</p>
                        @endif
                        <h2 class="section-title mt-1">{{ $shopCategory->title ?? 'Menu Categories' }}</h2>
                        @if ($shopCategory->body)
                            <p class="mt-2 max-w-xl text-sm text-brand-muted">{{ $shopCategory->body }}</p>
                        @endif
                    </div>
                    <a href="{{ route('shop.index') }}" class="text-sm font-semibold uppercase tracking-wide text-brand-red transition hover:underline">
                        Full menu →
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                    @foreach ($categories as $category)
                        <a
                            href="{{ route('shop.index', ['category' => $category->id]) }}"
                            class="menu-category-card group"
                            data-storefront-reveal
                            style="--reveal-delay: {{ $loop->index * 90 }}ms"
                        >
                            <img
                                src="{{ $category->storefrontImageUrl() }}"
                                alt="{{ $category->name }}"
                                class="menu-category-card-image"
                                loading="lazy"
                                decoding="async"
                            >
                            <span class="menu-category-card-overlay" aria-hidden="true"></span>
                            <span class="menu-category-card-content">
                                <span class="menu-category-card-label">{{ $category->name }}</span>
                                @if ($category->description)
                                    <span class="menu-category-card-desc">{{ \Illuminate\Support\Str::limit($category->description, 48) }}</span>
                                @endif
                                <span class="menu-category-card-cta">View dishes</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Menu sections by category --}}
    @if ($menuCategories->isNotEmpty())
        <section class="bg-transparent py-12 sm:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 hidden items-start justify-between gap-4 sm:flex sm:gap-6" data-storefront-reveal>
                    <div class="min-w-0 max-w-2xl flex-1">
                        <p class="section-eyebrow">From the kitchen</p>
                        <h2 class="section-title mt-1">Order from the menu</h2>
                        <p class="mt-2 text-sm text-brand-muted">Browse dishes by category — pick a portion and spice level at checkout.</p>
                    </div>
                    <div class="menu-section-accent" aria-hidden="true">
                        <img
                            src="{{ asset('images/brand/menu.jpeg') }}"
                            alt=""
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                </div>

                <div class="space-y-14">
                    @foreach ($menuCategories as $category)
                        <div id="menu-{{ $category->slug }}" data-storefront-reveal>
                            <div class="mb-5 flex items-end justify-between gap-4 border-b border-neutral-200 pb-3">
                                <div>
                                    <h3 class="font-serif text-2xl font-bold tracking-tight text-brand-black sm:text-3xl">{{ $category->name }}</h3>
                                    @if ($category->description)
                                        <p class="mt-1 text-sm text-brand-muted">{{ $category->description }}</p>
                                    @endif
                                </div>
                                <a
                                    href="{{ route('shop.index', ['category' => $category->id]) }}"
                                    class="shrink-0 text-xs font-semibold uppercase tracking-wide text-brand-red hover:underline"
                                >
                                    See all
                                </a>
                            </div>

                            <div class="menu-item-rail">
                                @foreach ($category->menuProducts as $product)
                                    @include('storefront.partials.menu-item-card', [
                                        'product' => $product,
                                        'revealDelay' => $loop->index * 80,
                                    ])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 text-center" data-storefront-reveal>
                    <a href="{{ route('shop.index') }}" class="btn-yellow px-8 py-3">View full menu</a>
                </div>
            </div>
        </section>
    @endif

    {{-- Mobile-only category cards (shown after menu section) --}}
    @if ($shopCategory?->is_active && $categories->isNotEmpty())
        <section class="border-y border-neutral-200 bg-transparent py-10 sm:hidden">
            <div class="mx-auto max-w-7xl px-4">
                <div class="flex flex-col gap-4" data-storefront-reveal>
                    <div>
                        <h2 class="section-title mt-1">Browse Our Menu</h2>
                    </div>
                    <a href="{{ route('shop.index') }}" class="text-sm font-semibold uppercase tracking-wide text-brand-red transition hover:underline">
                        Full menu →
                    </a>
                </div>

                <div class="menu-category-rail">
                    @foreach ($categories as $category)
                        <a
                            href="{{ route('shop.index', ['category' => $category->id]) }}"
                            class="menu-category-card menu-category-rail-item group"
                            data-storefront-reveal
                            style="--reveal-delay: {{ $loop->index * 90 }}ms"
                        >
                            <img
                                src="{{ $category->storefrontImageUrl() }}"
                                alt="{{ $category->name }}"
                                class="menu-category-card-image"
                                loading="lazy"
                                decoding="async"
                            >
                            <span class="menu-category-card-overlay" aria-hidden="true"></span>
                            <span class="menu-category-card-content">
                                    <span class="menu-category-card-label text-brand-yellow">{{ $category->name }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- How it works (desktop/tablet position) --}}
    <section class="hidden border-y border-neutral-200 bg-white/70 py-14 sm:block sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-storefront-reveal>
                <p class="section-eyebrow">Simple ordering</p>
                <h2 class="section-title mt-1">How it works</h2>
            </div>
            <div class="mt-10 grid gap-6 sm:grid-cols-3">
                <div class="rounded-xl border border-neutral-200 bg-brand-white p-6 text-center" data-storefront-reveal style="--reveal-delay: 0ms">
                    <p class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-red font-serif text-xl font-bold text-white">1</p>
                    <h3 class="mt-4 text-sm font-semibold uppercase tracking-wide">Pick your dishes</h3>
                    <p class="mt-2 text-sm text-brand-muted">Browse the menu and choose portions and options.</p>
                </div>
                <div class="rounded-xl border border-neutral-200 bg-brand-white p-6 text-center" data-storefront-reveal style="--reveal-delay: 100ms">
                    <p class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-yellow font-serif text-xl font-bold text-brand-black">2</p>
                    <h3 class="mt-4 text-sm font-semibold uppercase tracking-wide">Checkout securely</h3>
                    <p class="mt-2 text-sm text-brand-muted">Pay with mobile money or card via Paystack.</p>
                </div>
                <div class="rounded-xl border border-neutral-200 bg-brand-white p-6 text-center" data-storefront-reveal style="--reveal-delay: 200ms">
                    <p class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-black font-serif text-xl font-bold text-brand-yellow">3</p>
                    <h3 class="mt-4 text-sm font-semibold uppercase tracking-wide">Enjoy hot food</h3>
                    <p class="mt-2 text-sm text-brand-muted">We cook fresh and deliver across Accra.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA band --}}
    @if ($cta?->is_active)
        <section class="restaurant-cta relative overflow-hidden bg-brand-black py-14 text-white sm:py-16">
            <div
                class="restaurant-cta-image"
                style="background-image: url('{{ asset('images/brand/food-hero-2.jpg') }}');"
                aria-hidden="true"
            ></div>
            <div class="restaurant-cta-fade" aria-hidden="true"></div>

            <div class="relative z-10 mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-4 sm:flex-row sm:items-center sm:px-6 lg:px-8">
                <div class="max-w-xl" data-storefront-reveal>
                    @if ($cta->eyebrow)
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-brand-yellow">{{ $cta->eyebrow }}</p>
                    @endif
                    @if ($cta->title)
                        <h2 class="mt-3 font-serif text-3xl font-bold leading-tight sm:text-4xl">{{ $cta->title }}</h2>
                    @endif
                    @if ($cta->body)
                        <p class="mt-3 text-sm text-neutral-300 sm:text-base">{{ $cta->body }}</p>
                    @endif
                </div>
                <div class="flex w-full flex-row gap-3 sm:w-auto sm:flex-row" data-storefront-reveal style="--reveal-delay: 120ms">
                    @if ($cta->primary_label)
                        <a href="{{ $cta->resolvedUrl($cta->primary_url) }}" class="btn-yellow flex-1 px-8 py-3 text-center sm:w-auto sm:flex-none">{{ $cta->primary_label }}</a>
                    @endif
                    @if ($cta->secondary_label)
                        <a href="{{ $cta->resolvedUrl($cta->secondary_url) }}" class="hero-btn-outline flex-1 px-8 py-3 text-center sm:w-auto sm:flex-none">{{ $cta->secondary_label }}</a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Popular dishes --}}
    @if ($newArrivals?->is_active)
        <section class="bg-transparent py-14 sm:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end" data-storefront-reveal>
                    <div>
                        @if ($newArrivals->eyebrow)
                            <p class="section-eyebrow">{{ $newArrivals->eyebrow }}</p>
                        @endif
                        @if ($newArrivals->title)
                            <h2 class="section-title mt-1">{{ $newArrivals->title }}</h2>
                        @endif
                        @if ($newArrivals->body)
                            <p class="mt-2 text-brand-muted">{{ $newArrivals->body }}</p>
                        @endif
                    </div>
                    @if ($newArrivals->primary_label)
                        <a href="{{ $newArrivals->resolvedUrl($newArrivals->primary_url) }}" class="text-sm font-semibold uppercase tracking-wide text-brand-red transition hover:underline">
                            {{ $newArrivals->primary_label }} →
                        </a>
                    @endif
                </div>

                <div id="home-new-arrivals-grid" class="menu-item-rail mt-8">
                    @forelse ($featuredProducts as $product)
                        @include('storefront.partials.menu-item-card', [
                            'product' => $product,
                            'revealDelay' => $loop->index * 80,
                        ])
                    @empty
                        <p class="w-full text-brand-muted sm:col-span-full">Dishes will appear here once added in the admin dashboard.</p>
                    @endforelse
                </div>

                @include('storefront.partials.load-more-button', [
                    'hasMore' => $featuredProducts->hasMorePages(),
                    'url' => route('home.new-arrivals'),
                    'target' => '#home-new-arrivals-grid',
                    'nextPage' => $featuredProducts->currentPage() + 1,
                ])
            </div>
        </section>
    @endif

    {{-- How it works (mobile position below popular dishes) --}}
    <section class="border-y border-neutral-200 bg-white/70 py-14 sm:hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-storefront-reveal>
                <p class="section-eyebrow">Simple ordering</p>
                <h2 class="section-title mt-1">How it works</h2>
            </div>
            <div class="mt-10 grid gap-6 sm:grid-cols-3">
                <div class="rounded-xl border border-neutral-200 bg-brand-white p-6 text-center" data-storefront-reveal style="--reveal-delay: 0ms">
                    <p class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-red font-serif text-xl font-bold text-white">1</p>
                    <h3 class="mt-4 text-sm font-semibold uppercase tracking-wide">Pick your dishes</h3>
                    <p class="mt-2 text-sm text-brand-muted">Browse the menu and choose portions and options.</p>
                </div>
                <div class="rounded-xl border border-neutral-200 bg-brand-white p-6 text-center" data-storefront-reveal style="--reveal-delay: 100ms">
                    <p class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-yellow font-serif text-xl font-bold text-brand-black">2</p>
                    <h3 class="mt-4 text-sm font-semibold uppercase tracking-wide">Checkout securely</h3>
                    <p class="mt-2 text-sm text-brand-muted">Pay with mobile money or card via Paystack.</p>
                </div>
                <div class="rounded-xl border border-neutral-200 bg-brand-white p-6 text-center" data-storefront-reveal style="--reveal-delay: 200ms">
                    <p class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-black font-serif text-xl font-bold text-brand-yellow">3</p>
                    <h3 class="mt-4 text-sm font-semibold uppercase tracking-wide">Enjoy hot food</h3>
                    <p class="mt-2 text-sm text-brand-muted">We cook fresh and deliver across Accra.</p>
                </div>
            </div>
        </div>
    </section>

    @include('storefront.partials.testimonials', [
        'testimonials' => $testimonials,
        'header' => $sections->get(HomeSection::KEY_TESTIMONIALS_HEADER),
    ])

    @include('storefront.partials.delivery-notice', ['section' => $sections->get(HomeSection::KEY_DELIVERY_NOTICE)])
@endsection
