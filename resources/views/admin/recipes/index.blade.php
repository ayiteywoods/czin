@extends('layouts.admin')

@section('heading', 'Recipes')
@section('subheading', 'Ingredients by product')

@section('content')
    <form method="GET" action="{{ route('admin.recipes.index') }}" class="mb-6 flex gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products…" class="input-field max-w-sm">
        <button type="submit" class="btn-outline">Search</button>
    </form>

    <x-admin-table-panel :page-ids="$products->pluck('id')">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <th class="admin-table-cell admin-cell-primary font-medium">Product</th>
                    <th class="admin-table-cell admin-col-md font-medium">Ingredients</th>
                    <th class="admin-table-cell admin-col-actions text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <x-admin-table-leading-cells :id="$product->id" :number="$products->firstItem() + $loop->index" />
                        <td class="admin-table-cell admin-cell-primary font-medium">{{ $product->name }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">{{ $product->recipe_items_count }}</td>
                        <td class="admin-table-cell admin-col-actions text-right">
                            <a href="{{ route('admin.recipes.edit', $product) }}" class="btn-outline px-3 py-1.5 text-xs">Edit recipe</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="admin-table-cell py-8 text-center text-brand-muted">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$products" />
@endsection
