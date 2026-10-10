@php
    use App\Enums\PromotionType;
    $promotion = $promotion ?? null;
    $selectedCategoryIds = collect(old('category_ids', $promotion?->targetCategoryIds() ?? []))->map(fn ($id) => (string) $id)->all();
    $selectedProductIds = collect(old('product_ids', $promotion?->targetProductIds() ?? []))->map(fn ($id) => (string) $id)->all();
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

    <div class="sm:col-span-2 space-y-3 rounded-xl border border-neutral-200 p-4">
        <div>
            <label class="block text-sm font-medium">Categories</label>
            <p class="mt-1 text-xs text-brand-muted">Tick one or more categories. Applies to all items in each selected category (and subcategories).</p>
        </div>
        <div class="grid gap-2 sm:grid-cols-2">
            @foreach ($categories as $category)
                <label class="flex items-center gap-3 rounded-lg border border-neutral-100 px-3 py-2">
                    <input
                        type="checkbox"
                        name="category_ids[]"
                        value="{{ $category->id }}"
                        class="h-4 w-4 rounded border-neutral-300 text-brand-red"
                        @checked(in_array((string) $category->id, $selectedCategoryIds, true))
                    >
                    <span class="text-sm">{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
        @error('category_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('category_ids.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2 space-y-3 rounded-xl border border-neutral-200 p-4">
        <div>
            <label class="block text-sm font-medium">Products</label>
            <p class="mt-1 text-xs text-brand-muted">Optional. Tick specific products to include. You can combine these with categories.</p>
        </div>
        <div class="max-h-64 space-y-2 overflow-y-auto pr-1">
            @foreach ($products as $product)
                <label class="flex items-center gap-3 rounded-lg border border-neutral-100 px-3 py-2">
                    <input
                        type="checkbox"
                        name="product_ids[]"
                        value="{{ $product->id }}"
                        class="h-4 w-4 rounded border-neutral-300 text-brand-red"
                        @checked(in_array((string) $product->id, $selectedProductIds, true))
                    >
                    <span class="text-sm">{{ $product->name }}</span>
                </label>
            @endforeach
        </div>
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
