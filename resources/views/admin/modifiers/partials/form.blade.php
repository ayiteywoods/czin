@php
    $group = $group ?? null;
    $modifiers = old('modifiers', $group?->modifiers?->map(fn ($m) => [
        'id' => $m->id,
        'name' => $m->name,
        'price' => $m->price,
        'is_active' => $m->is_active,
        'sort_order' => $m->sort_order,
    ])->values()->all() ?? [['name' => '', 'price' => 0, 'is_active' => true, 'sort_order' => 0]]);
    $selectedProducts = collect(old('product_ids', $group?->products?->pluck('id')->all() ?? []))->map(fn ($id) => (string) $id);
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-form-label :required="true">Group name</x-form-label>
        <input type="text" name="name" value="{{ old('name', $group?->name) }}" required class="input-field">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Min selections</label>
        <input type="number" name="min_selections" min="0" value="{{ old('min_selections', $group?->min_selections ?? 0) }}" class="input-field">
    </div>
    <div>
        <label class="block text-sm font-medium">Max selections</label>
        <input type="number" name="max_selections" min="1" value="{{ old('max_selections', $group?->max_selections ?? 1) }}" class="input-field">
    </div>
    <div class="sm:col-span-2 flex flex-wrap gap-4">
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_required" value="1" class="rounded-none border-neutral-300 text-brand-red focus:ring-brand-red" @checked(old('is_required', $group?->is_required ?? false))>
            Required
        </label>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="rounded-none border-neutral-300 text-brand-red focus:ring-brand-red" @checked(old('is_active', $group?->is_active ?? true))>
            Active
        </label>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium">Attach to products</label>
        <select name="product_ids[]" multiple class="input-field min-h-32">
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected($selectedProducts->contains((string) $product->id))>{{ $product->name }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-brand-muted">Hold Cmd/Ctrl to select multiple.</p>
    </div>
</div>

<div class="mt-6 space-y-3" x-data="{ rows: {{ Js::from($modifiers) }} }">
    <div class="flex items-center justify-between">
        <h3 class="font-medium">Modifiers</h3>
        <button type="button" class="btn-outline px-3 py-1.5 text-xs" @click="rows.push({ name: '', price: 0, is_active: true, sort_order: rows.length })">Add modifier</button>
    </div>
    <template x-for="(row, index) in rows" :key="index">
        <div class="grid gap-3 rounded border border-neutral-200 p-3 sm:grid-cols-4">
            <input type="hidden" :name="`modifiers[${index}][id]`" :value="row.id || ''">
            <div class="sm:col-span-2">
                <input type="text" :name="`modifiers[${index}][name]`" x-model="row.name" placeholder="Name" class="input-field" required>
            </div>
            <div>
                <input type="number" step="0.01" :name="`modifiers[${index}][price]`" x-model="row.price" placeholder="Price" class="input-field">
            </div>
            <div class="flex items-center justify-between gap-2">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" :name="`modifiers[${index}][is_active]`" value="1" x-model="row.is_active" class="rounded-none border-neutral-300 text-brand-red focus:ring-brand-red">
                    Active
                </label>
                <button type="button" class="text-xs text-brand-red" @click="rows.splice(index, 1)">Remove</button>
            </div>
        </div>
    </template>
</div>
