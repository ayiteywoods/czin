@extends('layouts.storefront')

@section('title', $product->name.' - CZIN')

@section('content')
    @include('storefront.partials.product-hero')

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="product-detail-layout">
            <div class="product-detail-gallery card p-4 sm:p-5" data-storefront-reveal>
                <x-product-gallery :product="$product" />
            </div>

            <div class="product-detail-purchase card p-6 sm:p-8" data-storefront-reveal style="--reveal-delay: 120ms">
                @if ($product->discount_price)
                    <span class="inline-block rounded-xl bg-brand-yellow px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-brand-black">Special</span>
                @endif

                <div class="mt-2 flex items-start justify-between gap-4">
                    <h1 class="min-w-0 flex-1 text-2xl font-semibold uppercase tracking-wide sm:text-3xl">{{ $product->name }}</h1>
                    <div class="flex shrink-0 items-center gap-2">
                        <x-product-favorite-button :product="$product" />
                        {{-- Share disabled for now
                        <x-product-share-button :product="$product" label />
                        --}}
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <span class="text-3xl font-semibold text-brand-red">{{ config('shop.currency_symbol') }} {{ number_format($product->sellingPrice(), 2) }}</span>
                    @if ($product->discount_price)
                        <span class="text-lg text-brand-muted line-through">{{ config('shop.currency_symbol') }} {{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <p class="mt-6 leading-relaxed text-neutral-600">{{ $product->description }}</p>

                @if ($product->variants->isNotEmpty())
                    <form
                        action="{{ route('cart.store') }}"
                        method="POST"
                        class="mt-8 space-y-6 border-t border-neutral-200 pt-8"
                        x-data="{
                            upsellOpen: false,
                            bypassUpsell: false,
                            upsellAdding: null,
                            upsellAdded: {},
                            async quickAdd(productId) {
                                if (this.upsellAdding || this.upsellAdded[productId]) {
                                    return;
                                }

                                this.upsellAdding = productId;

                                try {
                                    const response = await fetch(@js(route('cart.quick-add')), {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': @js(csrf_token()),
                                        },
                                        body: JSON.stringify({ product_id: productId }),
                                    });

                                    const data = await response.json();

                                    if (! response.ok) {
                                        throw new Error(data.message || 'Could not add this item.');
                                    }

                                    this.upsellAdded[productId] = true;
                                    this.updateCartBadge(data.cart_count);
                                } catch (error) {
                                    window.alert(error.message || 'Could not add this item.');
                                } finally {
                                    this.upsellAdding = null;
                                }
                            },
                            updateCartBadge(count) {
                                const cartLink = document.querySelector('header a[href*=\'cart\']');

                                if (! cartLink) {
                                    return;
                                }

                                let badge = cartLink.querySelector('span.rounded-full');

                                if (count <= 0) {
                                    badge?.remove();

                                    return;
                                }

                                if (! badge) {
                                    badge = document.createElement('span');
                                    badge.className = 'absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-brand-red text-[10px] font-bold text-white';
                                    cartLink.appendChild(badge);
                                }

                                badge.textContent = String(count);
                            },
                        }"
                        x-ref="cartForm"
                        @submit="if (!bypassUpsell) { $event.preventDefault(); upsellOpen = true; }"
                        @keydown.escape.window="upsellOpen = false"
                    >
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        @if ($errors->any())
                            <div class="border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-900">
                                <ul class="space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <x-product-variant-picker :product="$product" />

                        @include('storefront.partials.cart-upsell-modal', ['upsellProducts' => $upsellProducts])
                    </form>
                @else
                    <div class="mt-8 border-t border-neutral-200 pt-8 text-sm text-brand-muted">
                        This dish has no portion or option combinations configured yet.
                    </div>
                @endif

                <div class="mt-8 border-t border-neutral-200 pt-8">
                    <x-product-delivery-info />
                </div>
            </div>
        </div>

        @if ($relatedProducts->isNotEmpty())
            <section class="mt-20 border-t border-neutral-200 pt-16" data-storefront-reveal>
                <h2 class="section-title">You may also like</h2>
                <div class="menu-item-rail mt-8">
                    @foreach ($relatedProducts as $related)
                        @include('storefront.partials.menu-item-card', [
                            'product' => $related,
                            'revealDelay' => $loop->index * 80,
                        ])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
