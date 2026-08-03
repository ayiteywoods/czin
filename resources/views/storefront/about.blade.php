@extends('layouts.storefront')

@section('title', 'About Us - CZIN')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    <x-page-hero
        eyebrow="About us"
        :title="$page->title"
        :description="$settings->aboutHeroDescription()"
        icon="building"
        :stats="[
            ['value' => 'Fresh', 'label' => 'Daily', 'icon' => 'sparkle', 'tone' => 'red'],
            ['value' => 'Accra', 'label' => 'Delivery', 'icon' => 'truck', 'tone' => 'white'],
            ['value' => 'Made', 'label' => 'To order', 'icon' => 'bag', 'tone' => 'red'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ route('shop.index') }}" class="btn-primary">View menu</a>
            <a href="{{ route('pages.show', \App\Models\Page::SLUG_CONTACT) }}" class="hero-btn-outline px-5 py-2.5">Contact us</a>
        </x-slot:actions>
    </x-page-hero>

    <section class="border-b border-neutral-200 bg-brand-white" data-storefront-reveal>
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="overflow-hidden border border-neutral-200 bg-neutral-100">
                <img
                    src="{{ $settings->aboutImageUrl() }}"
                    alt="{{ config('shop.store_name') }} restaurant"
                    class="aspect-[21/9] w-full object-cover"
                >
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_280px]">
            <article class="border border-neutral-200 bg-white p-6 sm:p-8" data-storefront-reveal>
                <h2 class="text-2xl font-semibold uppercase tracking-wide text-brand-black">Our story</h2>
                <div class="prose prose-neutral mt-6 max-w-none prose-headings:tracking-tight prose-a:text-brand-red prose-a:no-underline hover:prose-a:underline">
                    {!! Str::markdown($page->body ?? '') !!}
                </div>
            </article>

            <aside class="space-y-4" data-storefront-reveal style="--reveal-delay: 120ms">
                <div class="border border-neutral-200 bg-brand-light p-5">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-brand-black">Visit us</h3>
                    <ul class="mt-4 space-y-3 text-sm text-brand-muted">
                        <li>{{ config('shop.contact_address') }}</li>
                        <li>
                            <a href="mailto:{{ config('shop.contact_email') }}" class="transition hover:text-brand-red">
                                {{ config('shop.contact_email') }}
                            </a>
                        </li>
                        <li>{{ config('shop.contact_phone') }}</li>
                    </ul>
                </div>

                <div class="border border-neutral-200 bg-white p-5">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-brand-black">Quick links</h3>
                    <div class="mt-4 space-y-2 text-sm">
                        <a href="{{ route('shop.index') }}" class="block text-brand-muted transition hover:text-brand-red">Full menu</a>
                        <a href="{{ route('pages.show', \App\Models\Page::SLUG_DELIVERY) }}" class="block text-brand-muted transition hover:text-brand-red">Delivery info</a>
                        <a href="{{ route('pages.show', \App\Models\Page::SLUG_RETURNS) }}" class="block text-brand-muted transition hover:text-brand-red">Order issues</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="border-y border-neutral-200 bg-brand-black text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div data-storefront-reveal>
                <p class="section-eyebrow text-brand-yellow">Why choose us</p>
                <h2 class="mt-3 text-2xl font-semibold uppercase tracking-wide sm:text-3xl">Cooked fresh. Delivered hot.</h2>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-3">
                <div class="border border-neutral-800 bg-neutral-900/50 p-6" data-storefront-reveal style="--reveal-delay: 0ms">
                    <p class="text-3xl font-semibold text-brand-yellow">01</p>
                    <h3 class="mt-4 text-lg font-semibold uppercase tracking-wide">Kitchen fresh</h3>
                    <p class="mt-2 text-sm leading-relaxed text-neutral-400">
                        Meals are prepared to order so flavours stay bold and portions arrive the way they should.
                    </p>
                </div>
                <div class="border border-neutral-800 bg-neutral-900/50 p-6" data-storefront-reveal style="--reveal-delay: 100ms">
                    <p class="text-3xl font-semibold text-brand-yellow">02</p>
                    <h3 class="mt-4 text-lg font-semibold uppercase tracking-wide">Reliable delivery</h3>
                    <p class="mt-2 text-sm leading-relaxed text-neutral-400">
                        We deliver across Accra with dispatch updates and support when you need it.
                    </p>
                </div>
                <div class="border border-neutral-800 bg-neutral-900/50 p-6" data-storefront-reveal style="--reveal-delay: 200ms">
                    <p class="text-3xl font-semibold text-brand-yellow">03</p>
                    <h3 class="mt-4 text-lg font-semibold uppercase tracking-wide">Easy checkout</h3>
                    <p class="mt-2 text-sm leading-relaxed text-neutral-400">
                        Order with confidence using mobile money, cards, and Paystack — then track your meal.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 text-center sm:px-6 lg:px-8 lg:py-20" data-storefront-reveal>
        <h2 class="text-2xl font-semibold uppercase tracking-wide text-brand-black">Ready to order?</h2>
        <p class="mx-auto mt-3 max-w-xl text-sm text-brand-muted sm:text-base">
            Browse the menu and get fresh CZIN meals delivered to your door.
        </p>
        <a href="{{ route('shop.index') }}" class="btn-primary mt-8 inline-flex">Browse the menu</a>
    </section>
@endsection
