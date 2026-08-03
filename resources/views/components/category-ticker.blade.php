@php
    $staticItems = [
        ['label' => 'Today’s Specials', 'url' => route('shop.index'), 'icon' => 'sparkle'],
        ['label' => 'Full Menu', 'url' => route('shop.index'), 'icon' => 'bag'],
        ['label' => 'Hot Deals', 'url' => route('shop.index'), 'icon' => 'tag'],
    ];

    $tickerItems = collect($staticItems);

    foreach ($floatingCategories ?? [] as $category) {
        $tickerItems->push([
            'label' => $category->name,
            'url' => route('shop.index', ['category' => $category->id]),
            'icon' => $category->storefrontIcon(),
        ]);
    }

    $tickerItems->push(['label' => 'Fast Delivery', 'url' => null, 'icon' => 'truck']);

    $tickerItems = $tickerItems->merge($tickerItems)->merge($tickerItems);
@endphp

<div class="ticker-bar !bg-white border-b border-neutral-200" aria-label="Browse menu" style="background-color: #ffffff;">
    <div class="ticker-track">
        @foreach ([1, 2] as $copy)
            <div class="ticker-content" @if($copy === 2) aria-hidden="true" @endif>
                @foreach ($tickerItems as $item)
                    @if ($item['url'])
                        <a href="{{ $item['url'] }}" class="ticker-item">
                            <x-nav-icon :icon="$item['icon']" class="h-2.5 w-2.5 shrink-0 text-brand-red" />
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @else
                        <span class="ticker-item">
                            <x-nav-icon :icon="$item['icon']" class="h-2.5 w-2.5 shrink-0 text-brand-red" />
                            <span>{{ $item['label'] }}</span>
                        </span>
                    @endif
                    <span class="ticker-separator" aria-hidden="true">&bull;</span>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
