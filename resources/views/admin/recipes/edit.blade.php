@php
    $items = old('items', $product->recipeItems->map(fn ($item) => [
        'ingredient_name' => $item->ingredient_name,
        'quantity' => $item->quantity,
        'unit' => $item->unit,
        'cost_per_unit' => $item->cost_per_unit,
    ])->values()->all() ?: [['ingredient_name' => '', 'quantity' => 1, 'unit' => '', 'cost_per_unit' => 0]]);
@endphp

@extends('layouts.admin')

@section('heading', 'Edit recipe')
@section('subheading', $product->name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.recipes.index') }}" class="text-sm text-brand-red hover:underline">← Back to recipes</a>
    </div>

    <form method="POST" action="{{ route('admin.recipes.update', $product) }}" class="card max-w-3xl space-y-4 p-6" x-data="{ rows: {{ Js::from($items) }} }">
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between">
            <h2 class="font-semibold">Ingredients</h2>
            <button type="button" class="btn-outline px-3 py-1.5 text-xs" @click="rows.push({ ingredient_name: '', quantity: 1, unit: '', cost_per_unit: 0 })">Add ingredient</button>
        </div>

        <template x-for="(row, index) in rows" :key="index">
            <div class="grid gap-3 rounded border border-neutral-200 p-3 sm:grid-cols-4">
                <div class="sm:col-span-2">
                    <input type="text" :name="`items[${index}][ingredient_name]`" x-model="row.ingredient_name" placeholder="Ingredient" class="input-field" required>
                </div>
                <div>
                    <input type="number" step="0.001" :name="`items[${index}][quantity]`" x-model="row.quantity" placeholder="Qty" class="input-field" required>
                </div>
                <div class="flex gap-2">
                    <input type="text" :name="`items[${index}][unit]`" x-model="row.unit" placeholder="Unit" class="input-field">
                    <button type="button" class="text-xs text-brand-red" @click="rows.splice(index, 1)">×</button>
                </div>
                <div class="sm:col-span-2">
                    <input type="number" step="0.01" :name="`items[${index}][cost_per_unit]`" x-model="row.cost_per_unit" placeholder="Cost per unit" class="input-field">
                </div>
            </div>
        </template>

        <button type="submit" class="btn-primary">Save recipe</button>
    </form>
@endsection
