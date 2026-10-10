@php
    use App\Enums\PromotionType;
    $promotion = $promotion ?? null;
    $selectedCategoryIds = collect(old('category_ids', $promotion?->targetCategoryIds() ?? []))->map(fn ($id) => (int) $id)->values()->all();
    $selectedProductIds = collect(old('product_ids', $promotion?->targetProductIds() ?? []))->map(fn ($id) => (int) $id)->values()->all();
    $productOptions = $products->map(fn ($product) => [
        'id' => (int) $product->id,
        'name' => $product->name,
    ])->values()->all();
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-form-label :required="true">Name</x-form-label>
        <input type="text" name="name" value="{{ old('name', $promotion?->name) }}" required class="input-field">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <x-form-label :required="true">Type</x-form-label>
        <select name="type" required class="input-field">
            @foreach (PromotionType::cases() as $type)
                <option value="{{ $type->value }}" @selected(old('type', $promotion?->type?->value) === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <x-form-label :required="true">Value</x-form-label>
        <input type="number" step="0.01" name="value" value="{{ old('value', $promotion?->value) }}" required class="input-field">
        @error('value')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Starts at</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $promotion?->starts_at?->format('Y-m-d\TH:i')) }}" class="input-field">
        @error('starts_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Ends at</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $promotion?->ends_at?->format('Y-m-d\TH:i')) }}" class="input-field">
        @error('ends_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div
        class="sm:col-span-2 space-y-3"
        x-data="{
            selected: @js($selectedCategoryIds),
            toggle(id) {
                id = Number(id);
                if (this.selected.includes(id)) {
                    this.selected = this.selected.filter((value) => value !== id);
                } else {
                    this.selected.push(id);
                }
            },
            isSelected(id) {
                return this.selected.includes(Number(id));
            }
        }"
    >
        <div>
            <label class="block text-sm font-medium">Categories</label>
            <p class="mt-1 text-xs text-brand-muted">Select one or more. Applies to all items in each category (and subcategories).</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach ($categories as $category)
                <label
                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-1.5 text-sm transition"
                    :class="isSelected({{ $category->id }}) ? 'border-brand-red bg-red-50 text-brand-black' : 'border-neutral-200 bg-white text-brand-muted'"
                >
                    <input
                        type="checkbox"
                        name="category_ids[]"
                        value="{{ $category->id }}"
                        class="h-4 w-4 rounded border-neutral-300 text-brand-red"
                        :checked="isSelected({{ $category->id }})"
                        @change="toggle({{ $category->id }})"
                    >
                    {{ $category->name }}
                </label>
            @endforeach
        </div>
        @error('category_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('category_ids.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div
        class="sm:col-span-2 space-y-3"
        x-data="{
            options: @js($productOptions),
            selected: @js($selectedProductIds),
            pick: '',
            add() {
                const id = Number(this.pick);
                if (! id || this.selected.includes(id)) {
                    this.pick = '';
                    return;
                }
                this.selected.push(id);
                this.pick = '';
            },
            remove(id) {
                this.selected = this.selected.filter((value) => value !== Number(id));
            },
            label(id) {
                return this.options.find((option) => option.id === Number(id))?.name || ('Product #' + id);
            },
            available() {
                return this.options.filter((option) => ! this.selected.includes(option.id));
            }
        }"
    >
        <div>
            <label class="block text-sm font-medium">Products</label>
            <p class="mt-1 text-xs text-brand-muted">Optional. Add specific products from the dropdown (same style as before). You can add more than one.</p>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row">
            <select class="input-field" x-model="pick">
                <option value="">— Select a product to add —</option>
                <template x-for="option in available()" :key="option.id">
                    <option :value="option.id" x-text="option.name"></option>
                </template>
            </select>
            <button type="button" class="btn-outline px-4 py-2 sm:shrink-0" @click="add()" :disabled="!pick">
                Add product
            </button>
        </div>

        <template x-if="selected.length">
            <div class="flex flex-wrap gap-2">
                <template x-for="id in selected" :key="id">
                    <span class="inline-flex max-w-full items-center gap-2 rounded-lg border border-neutral-200 bg-brand-light px-3 py-1.5 text-sm">
                        <input type="hidden" name="product_ids[]" :value="id">
                        <span class="truncate" x-text="label(id)"></span>
                        <button type="button" class="text-brand-muted hover:text-brand-red" @click="remove(id)" aria-label="Remove product">×</button>
                    </span>
                </template>
            </div>
        </template>

        <p class="text-xs text-brand-muted" x-show="selected.length === 0">No specific products added — category selection alone is enough if you only want category-wide promos.</p>

        @error('product_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('product_ids.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Start time</label>
        <input type="time" name="start_time" value="{{ old('start_time', $promotion?->start_time ? \Illuminate\Support\Str::of($promotion->start_time)->substr(0, 5) : '') }}" class="input-field">
    </div>
    <div>
        <label class="block text-sm font-medium">End time</label>
        <input type="time" name="end_time" value="{{ old('end_time', $promotion?->end_time ? \Illuminate\Support\Str::of($promotion->end_time)->substr(0, 5) : '') }}" class="input-field">
    </div>
    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="rounded-none border-neutral-300 text-brand-red focus:ring-brand-red" @checked(old('is_active', $promotion?->is_active ?? true))>
            Active
        </label>
    </div>
</div>
