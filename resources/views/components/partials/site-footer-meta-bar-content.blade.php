@php
    $footerSocialLinks = $socialLinks ?? [];
@endphp

<div class="space-y-3 sm:hidden">
    <div class="flex flex-wrap items-center justify-center gap-2">
        <a href="#" aria-label="Download on the App Store" class="inline-flex">
            <img
                src="{{ asset('images/brand/badge-app-store.svg') }}"
                alt="Download on the App Store"
                class="h-10 w-auto"
                loading="lazy"
                decoding="async"
            >
        </a>
        <a href="#" aria-label="Get it on Google Play" class="inline-flex">
            <img
                src="{{ asset('images/brand/badge-google-play.svg') }}"
                alt="Get it on Google Play"
                class="h-10 w-auto"
                loading="lazy"
                decoding="async"
            >
        </a>
    </div>

    @if (count($footerSocialLinks) > 0)
        <x-storefront-social-links :links="$footerSocialLinks" class="justify-center" />
    @endif

    <p @class([
        'text-center',
        'text-neutral-500' => $isDark,
        'text-brand-muted' => ! $isDark,
    ])>
        &copy; {{ date('Y') }} CZIN. All rights reserved.
    </p>
</div>

<p @class([
    'hidden text-left sm:block',
    'text-neutral-500' => $isDark,
    'text-brand-muted' => ! $isDark,
])>
    &copy; {{ date('Y') }} CZIN. All rights reserved.
</p>

<p @class([
    'inline-flex items-center justify-center gap-1.5 text-center sm:justify-end sm:text-right',
    'text-neutral-400' => $isDark,
    'text-brand-muted' => ! $isDark,
])>
    <svg @class([
        'h-3.5 w-3.5',
        'text-neutral-300' => $isDark,
        'text-brand-black' => ! $isDark,
    ]) fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/>
    </svg>
    <span>Powered by EdenLabs</span>
</p>
