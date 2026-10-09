@extends('layouts.admin')

@section('heading', 'Menu')
@section('subheading', 'Dishes and drinks available to order')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex items-center gap-2 text-sm">
            @foreach (request()->except(['per_page', 'page']) as $key => $value)
                @if (is_array($value))
                    @foreach ($value as $item)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <label for="products-per-page" class="whitespace-nowrap text-brand-muted">Show</label>
            <select
                id="products-per-page"
                name="per_page"
                class="input-field mt-0 w-24 py-1.5 text-sm"
                onchange="this.form.submit()"
            >
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>

        <a href="{{ route('admin.products.create') }}" class="btn-primary w-full text-center sm:w-auto">Add menu item</a>
    </div>

    <x-admin-table-panel :page-ids="$products->pluck('id')">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <x-admin-sort-th column="name" label="Dish" class="admin-cell-primary" />
                    <x-admin-sort-th column="sku" label="Item code" class="admin-col-md" />
                    <x-admin-sort-th column="price" label="Price" />
                    <x-admin-sort-th column="quantity" label="Portions" />
                    <x-admin-sort-th column="status" label="Status" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-actions text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <x-admin-table-leading-cells :id="$product->id" :number="$products->firstItem() + $loop->index" />
                        <td class="admin-table-cell admin-cell-primary">
                            <div class="font-medium">{{ $product->name }}</div>
                            <div class="text-brand-muted">{{ $product->category->name }}</div>
                            <div class="mt-1 text-xs text-brand-muted md:hidden">{{ $product->status->label() }}</div>
                        </td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $product->sku }}</td>
                        <td class="admin-table-cell whitespace-nowrap">GHS {{ number_format($product->sellingPrice(), 2) }}</td>
                        <td class="admin-table-cell whitespace-nowrap">
                            {{ $product->quantity }}
                            @if ($product->isLowStock())
                                <span class="text-amber-600">Low</span>
                            @endif
                        </td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">
                            <div>{{ $product->status->label() }}</div>
                            @if ($product->is_86ed)
                                <div class="mt-1 text-xs font-medium text-brand-red">86'd</div>
                            @elseif ($product->isScheduledForFuture())
                                <div class="mt-1 text-xs text-amber-700">Scheduled {{ $product->storefrontPublishLabel() }}</div>
                            @elseif ($product->storefrontPublishLabel())
                                <div class="mt-1 text-xs text-brand-muted">Live since {{ $product->storefrontPublishLabel() }}</div>
                            @endif
                        </td>
                        <td class="admin-table-cell admin-col-actions">
                            <div class="flex flex-col items-end gap-2">
                                <x-admin-table-actions
                                    :view-detail-url="route('admin.details.products', $product)"
                                    :edit-url="route('admin.products.edit', $product)"
                                    :delete-url="route('admin.products.destroy', $product)"
                                    delete-confirm="Remove this menu item permanently?"
                                />
                                <form method="POST" action="{{ route('admin.products.toggle-86', $product) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="whitespace-nowrap text-xs text-brand-red hover:underline">
                                        {{ $product->is_86ed ? 'Un-86' : '86' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="admin-table-cell py-8 text-center text-brand-muted">No menu items yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$products" />
@endsection
