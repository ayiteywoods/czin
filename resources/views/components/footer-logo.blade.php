@props([
    'href' => route('home'),
    'size' => 'header',
])

@php
    $settings = \App\Models\StoreSetting::current();
    $imageClass = match ($size) {
        'header' => 'h-11 w-auto max-w-[12rem] object-contain sm:h-12',
        default => 'h-10 w-auto max-w-[10rem] object-contain',
    };
@endphp

@if ($settings->hasCustomFooterLogo())
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center']) }}>
        <img
            src="{{ $settings->footerLogoUrl() }}"
            alt="{{ config('shop.store_name') }}"
            class="{{ $imageClass }}"
        >
    </a>
@else
    <x-logo :href="$href" :size="$size" variant="dark" {{ $attributes }} />
@endif
