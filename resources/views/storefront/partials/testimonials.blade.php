@php
    $header = $header ?? null;
@endphp

@if ($testimonials->isNotEmpty() && (! $header || $header->is_active))
    <section class="restaurant-reviews relative hidden overflow-hidden bg-brand-black py-14 text-white sm:block sm:py-16">
        <div
            class="restaurant-reviews-image"
            style="background-image: url('{{ asset('images/brand/food-hero-3.jpg') }}');"
            aria-hidden="true"
        ></div>
        <div class="restaurant-reviews-fade" aria-hidden="true"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center" data-storefront-reveal>
                @if ($header?->eyebrow)
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-brand-yellow">{{ $header->eyebrow }}</p>
                @endif
                @if ($header?->title)
                    <h2 class="mt-3 font-serif text-3xl font-bold leading-tight sm:text-4xl">{{ $header->title }}</h2>
                @endif
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    <div
                        class="rounded-xl border border-white/10 bg-white p-6 text-brand-black shadow-sm"
                        data-storefront-reveal
                        style="--reveal-delay: {{ $loop->index * 90 }}ms"
                    >
                        <div class="flex gap-0.5 text-brand-red">
                            @for ($i = 0; $i < $testimonial->rating; $i++)
                                <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="mt-4 text-sm leading-relaxed text-neutral-600">&ldquo;{{ $testimonial->quote }}&rdquo;</p>
                        <p class="mt-4 text-sm font-semibold text-brand-black">&mdash; {{ $testimonial->author_name }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
