@php
    $layout = $layout ?? 'menu';
@endphp

@foreach ($products as $product)
    @if ($layout === 'grid')
        @include('storefront.partials.product-card', ['product' => $product])
    @else
        @include('storefront.partials.menu-item-card', ['product' => $product])
    @endif
@endforeach
