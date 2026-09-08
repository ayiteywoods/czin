@props([
    'href' => route('home'),
    'size' => 'header',
    'variant' => 'light',
    'hideTextOnMobile' => false,
])

@php
    $imageClass = match ($size) {
        'header' => 'h-11 w-11 object-contain sm:h-12 sm:w-12',
        'admin' => 'h-10 w-10 object-contain',
        'auth' => 'h-14 w-14 object-contain',
        default => 'h-10 w-10 object-contain',
    };

    $textClass = match ($size) {
        'header' => 'h-7 w-auto object-contain sm:h-8',
        'admin' => 'h-5 w-auto object-contain',
        'auth' => 'h-7 w-auto object-contain sm:h-8',
        default => 'h-5 w-auto object-contain',
    };

    // light = black wordmark (keeps colored i-dot); on-red/dark = white wordmark
    $wordmarkSrc = $variant === 'light'
        ? asset('images/brand/ctext-on-light.png')
        : asset('images/brand/ctext.png');
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center gap-2']) }}>
    <img
        src="{{ asset('images/brand/clogo.png') }}"
        alt="{{ config('shop.store_name') }}"
        class="{{ $imageClass }}"
    >
    <img
        src="{{ $wordmarkSrc }}"
        alt=""
        aria-hidden="true"
        @class([$textClass, 'hidden sm:block' => $hideTextOnMobile])
    >
</a>
