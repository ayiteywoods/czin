@php
    use App\Enums\PromotionType;
    $promotion = $promotion ?? null;
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
    <div>
        <label class="block text-sm font-medium">Category</label>
        <select name="category_id" class="input-field">
            <option value="">— Select category —</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $promotion?->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-brand-muted">Applies only to items in this category (and its subcategories).</p>
        @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Product</label>
        <select name="product_id" class="input-field">
            <option value="">— Or select one product —</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected((string) old('product_id', $promotion?->product_id) === (string) $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-brand-muted">Optional. If set, only this product gets the promo (category is ignored). You must choose a category or a product.</p>
        @error('product_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
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
