@php
    use App\Enums\ReservationStatus;
    $reservation = $reservation ?? null;
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-form-label :required="true">Guest name</x-form-label>
        <input type="text" name="guest_name" value="{{ old('guest_name', $reservation?->guest_name) }}" required class="input-field">
        @error('guest_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Phone</label>
        <input type="text" name="guest_phone" value="{{ old('guest_phone', $reservation?->guest_phone) }}" class="input-field">
        @error('guest_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="guest_email" value="{{ old('guest_email', $reservation?->guest_email) }}" class="input-field">
        @error('guest_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <x-form-label :required="true">Party size</x-form-label>
        <input type="number" name="party_size" min="1" value="{{ old('party_size', $reservation?->party_size ?? 2) }}" required class="input-field">
        @error('party_size')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <x-form-label :required="true">Reserved at</x-form-label>
        <input type="datetime-local" name="reserved_at" value="{{ old('reserved_at', $reservation?->reserved_at?->format('Y-m-d\TH:i')) }}" required class="input-field">
        @error('reserved_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Duration (minutes)</label>
        <input type="number" name="duration_minutes" min="15" value="{{ old('duration_minutes', $reservation?->duration_minutes ?? 90) }}" class="input-field">
        @error('duration_minutes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Table</label>
        <select name="dining_table_id" class="input-field">
            <option value="">— None —</option>
            @foreach ($tables as $table)
                <option value="{{ $table->id }}" @selected((string) old('dining_table_id', $reservation?->dining_table_id) === (string) $table->id)>
                    {{ $table->code }} · {{ $table->name }}
                </option>
            @endforeach
        </select>
        @error('dining_table_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <x-form-label :required="true">Status</x-form-label>
        <select name="status" required class="input-field">
            @foreach (ReservationStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', $reservation?->status?->value ?? ReservationStatus::Pending->value) === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium">Notes</label>
        <textarea name="notes" rows="3" class="input-field">{{ old('notes', $reservation?->notes) }}</textarea>
        @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
