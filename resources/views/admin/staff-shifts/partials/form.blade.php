@php $shift = $shift ?? null; @endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-form-label :required="true">Staff member</x-form-label>
        <select name="user_id" required class="input-field">
            <option value="">Select</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('user_id', $shift?->user_id) === (string) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Location</label>
        <select name="location_id" class="input-field">
            <option value="">— None —</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected((string) old('location_id', $shift?->location_id) === (string) $location->id)>{{ $location->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-form-label :required="true">Starts at</x-form-label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $shift?->starts_at?->format('Y-m-d\TH:i')) }}" required class="input-field">
        @error('starts_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <x-form-label :required="true">Ends at</x-form-label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $shift?->ends_at?->format('Y-m-d\TH:i')) }}" required class="input-field">
        @error('ends_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Role label</label>
        <input type="text" name="role_label" value="{{ old('role_label', $shift?->role_label) }}" class="input-field" placeholder="e.g. Cashier">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium">Notes</label>
        <textarea name="notes" rows="2" class="input-field">{{ old('notes', $shift?->notes) }}</textarea>
    </div>
</div>
