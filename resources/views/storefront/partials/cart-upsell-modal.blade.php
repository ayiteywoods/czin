@props(['upsellProducts'])

<template x-teleport="body">
    <div
        x-show="upsellOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[200] flex items-center justify-center bg-black/75 p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="upsell-title"
        aria-describedby="upsell-body"
        @click.self="upsellOpen = false"
        x-effect="document.body.classList.toggle('overflow-hidden', upsellOpen)"
    >
        <div
            x-show="upsellOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl border-2 border-brand-red/40 bg-white shadow-2xl"
            @click.stop
        >
            <div class="border-b border-neutral-200 px-5 py-4 sm:px-6">
                <p class="inline-flex rounded-full bg-brand-red/10 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.22em] text-brand-red">
                    Chef's combo offer
                </p>
                <h3 id="upsell-title" class="mt-3 font-serif text-2xl font-bold text-brand-black">
                    Complete your order
                </h3>
                <p id="upsell-body" class="mt-2 text-sm leading-relaxed text-brand-muted">
                    Add a drink, side, or dessert before checkout.
                </p>
            </div>

            @if ($upsellProducts->isNotEmpty())
                <div class="flex-1 overflow-y-auto px-5 py-4 sm:px-6">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-brand-muted">Popular add-ons</p>
                    <ul class="mt-3 space-y-3">
                        @foreach ($upsellProducts as $upsellProduct)
                            <li class="flex items-center gap-3 rounded-xl border border-neutral-200 bg-brand-white p-2.5">
                                <img
                                    src="{{ $upsellProduct->storefrontImageUrl() }}"
                                    alt="{{ $upsellProduct->name }}"
                                    class="h-16 w-16 shrink-0 rounded-lg object-cover"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <div class="min-w-0 flex-1">
                                    @if ($upsellProduct->category)
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-brand-muted">
                                            {{ $upsellProduct->category->name }}
                                        </p>
                                    @endif
                                    <p class="truncate text-sm font-semibold text-brand-black">{{ $upsellProduct->name }}</p>
                                    <p class="mt-0.5 text-sm font-semibold text-brand-red">
                                        {{ config('shop.currency_symbol') }}{{ number_format($upsellProduct->sellingPrice(), 2) }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="btn-primary shrink-0 px-3 py-2 text-[10px] disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="upsellAdding === {{ $upsellProduct->id }} || upsellAdded[{{ $upsellProduct->id }}]"
                                    @click="quickAdd({{ $upsellProduct->id }})"
                                    x-text="upsellAdded[{{ $upsellProduct->id }}] ? 'Added' : (upsellAdding === {{ $upsellProduct->id }} ? '...' : 'Add')"
                                ></button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-2 border-t border-neutral-200 px-5 py-4 sm:px-6">
                <button
                    type="button"
                    class="btn-yellow w-full justify-center py-2.5"
                    @click="upsellOpen = false"
                >
                    Add more dishes
                </button>
                <button
                    type="button"
                    class="btn-primary w-full justify-center py-2.5"
                    @click="bypassUpsell = true; upsellOpen = false; $nextTick(() => $refs.cartForm.requestSubmit())"
                >
                    Continue to cart
                </button>
                <button
                    type="button"
                    class="w-full py-2 text-xs font-semibold uppercase tracking-wide text-brand-muted transition hover:text-brand-black"
                    @click="upsellOpen = false"
                >
                    Not now
                </button>
            </div>
        </div>
    </div>
</template>
