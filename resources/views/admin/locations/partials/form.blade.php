@php $location = $location ?? null; @endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-form-label :required="true">Name</x-form-label>
        <input type="text" name="name" value="{{ old('name', $location?->name) }}" required class="input-field">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium">Address</label>
        <input type="text" name="address" value="{{ old('address', $location?->address) }}" class="input-field">
        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $location?->phone) }}" class="input-field">
        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="flex flex-wrap items-end gap-4">
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="rounded-none border-neutral-300 text-brand-red focus:ring-brand-red" @checked(old('is_active', $location?->is_active ?? true))>
            Active
        </label>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_default" value="1" class="rounded-none border-neutral-300 text-brand-red focus:ring-brand-red" @checked(old('is_default', $location?->is_default ?? false))>
            Default location
        </label>
    </div>
</div>
