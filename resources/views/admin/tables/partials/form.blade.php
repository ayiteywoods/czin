@php
    use App\Enums\TableSize;
    use App\Enums\TableStatus;
@endphp

@php($table = $table ?? null)

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-form-label :required="true">Table code</x-form-label>
        <input type="text" name="code" value="{{ old('code', $table?->code) }}" required class="input-field" placeholder="T-01">
        @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <x-form-label :required="true">Display name</x-form-label>
        <input type="text" name="name" value="{{ old('name', $table?->name) }}" required class="input-field" placeholder="Table 1">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-form-label :required="true">Area</x-form-label>
        <input type="text" name="area" value="{{ old('area', $table?->area) }}" required class="input-field" placeholder="Main Hall">
        @error('area')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <x-form-label :required="true">Capacity</x-form-label>
        <input type="number" name="capacity" min="1" max="99" value="{{ old('capacity', $table?->capacity ?? 4) }}" required class="input-field">
        @error('capacity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-form-label :required="true">Size</x-form-label>
        <select name="size" class="input-field">
            @foreach (TableSize::cases() as $sizeOption)
                <option value="{{ $sizeOption->value }}" @selected(old('size', $table?->size?->value ?? TableSize::Small->value) === $sizeOption->value)>
                    {{ $sizeOption->label() }}
                </option>
            @endforeach
        </select>
        @error('size')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <x-form-label :required="true">Minimum spend ({{ config('shop.currency_symbol') }})</x-form-label>
        <input type="number" name="price" min="0" step="0.01" value="{{ old('price', $table?->price ?? 0) }}" required class="input-field">
        @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-form-label :required="true">Status</x-form-label>
        <select name="status" class="input-field">
            @foreach (TableStatus::cases() as $statusOption)
                <option value="{{ $statusOption->value }}" @selected(old('status', $table?->status?->value ?? TableStatus::Available->value) === $statusOption->value)>
                    {{ $statusOption->label() }}
                </option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <x-form-label>Sort order</x-form-label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $table?->sort_order ?? 0) }}" class="input-field">
        @error('sort_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
