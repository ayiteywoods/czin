@php
    $inStock = $product->isInStock();
    $description = \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 90);
@endphp

<article
    class="menu-item-card group"
    data-storefront-reveal
    @if (isset($revealDelay)) style="--reveal-delay: {{ $revealDelay }}ms" @endif
>
    <a href="{{ route('shop.show', $product) }}" class="menu-item-card-media" aria-hidden="true" tabindex="-1">
        <img
            src="{{ $product->storefrontImageUrl() }}"
            alt="{{ $product->name }}"
            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
            loading="lazy"
            decoding="async"
        >
    </a>

    <div class="menu-item-card-body">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                @if ($product->category)
                    <p class="menu-item-card-category">{{ $product->category->name }}</p>
                @endif
                <h3 class="menu-item-card-title">
                    <a href="{{ route('shop.show', $product) }}" class="transition hover:text-brand-red">
                        {{ $product->name }}
                    </a>
                </h3>
                @if ($description)
                    <p class="menu-item-card-desc hidden sm:block">{{ $description }}</p>
                @endif
            </div>
            <div class="menu-item-card-price shrink-0 text-right">
                <p class="font-semibold text-brand-red">
                    {{ config('shop.currency_symbol') }}{{ number_format($product->sellingPrice(), 2) }}
                </p>
                @if ($product->compareAtPrice())
                    <p class="text-xs text-brand-muted line-through">
                        {{ config('shop.currency_symbol') }}{{ number_format($product->compareAtPrice(), 2) }}
                    </p>
                @endif
            </div>
        </div>

        <div class="menu-item-card-actions">
            @if (! $inStock)
                <span class="text-xs font-semibold uppercase tracking-wide text-brand-muted">Unavailable</span>
            @elseif ($product->promotionBadge())
                <span class="inline-flex bg-brand-yellow px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-brand-black">{{ $product->promotionBadge() }}</span>
            @else
                <span class="hidden text-xs uppercase tracking-wide text-brand-muted sm:inline">Made to order</span>
            @endif

            <a
                href="{{ route('shop.show', $product) }}"
                class="btn-primary w-full justify-center px-3 py-2 text-[11px] sm:w-auto sm:px-4"
            >
                {{ $inStock ? 'Order' : 'View' }}
            </a>
        </div>
    </div>
</article>
