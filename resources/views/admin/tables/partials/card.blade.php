@props(['table'])

<article class="admin-table-card group">
    <div class="admin-table-card-header">
        <p class="admin-table-card-code">{{ $table->code }}</p>
        <x-admin-table-status-badge :status="$table->status" />
    </div>

    <div class="admin-table-card-visual" aria-hidden="true">
        <svg viewBox="0 0 120 72" class="h-20 w-full max-w-[140px] text-neutral-300" fill="currentColor">
            <rect x="34" y="24" width="52" height="24" rx="4" class="text-neutral-200" fill="currentColor"/>
            <circle cx="24" cy="20" r="5"/>
            <circle cx="96" cy="20" r="5"/>
            <circle cx="24" cy="52" r="5"/>
            <circle cx="96" cy="52" r="5"/>
            @if ($table->capacity >= 6)
                <circle cx="60" cy="12" r="5"/>
                <circle cx="60" cy="60" r="5"/>
            @endif
            @if ($table->capacity >= 8)
                <circle cx="42" cy="12" r="5"/>
                <circle cx="78" cy="12" r="5"/>
            @endif
        </svg>
    </div>

    <div class="admin-table-card-meta">
        <div>
            <p class="text-[10px] uppercase tracking-wide text-brand-muted">Size</p>
            <p class="text-sm font-medium text-brand-black">{{ $table->size->label() }}</p>
        </div>
        <div>
            <p class="text-[10px] uppercase tracking-wide text-brand-muted">Capacity</p>
            <p class="text-sm font-medium text-brand-black">{{ $table->capacityLabel() }}</p>
        </div>
        <div class="text-right">
            <p class="text-[10px] uppercase tracking-wide text-brand-muted">Min spend</p>
            <p class="text-sm font-semibold text-brand-red">{{ config('shop.currency_symbol') }}{{ number_format($table->price, 2) }}</p>
        </div>
    </div>

    <div class="admin-table-card-actions">
        <form method="POST" action="{{ route('admin.tables.update-status', $table) }}" class="min-w-0 flex-1">
            @csrf
            @method('PATCH')
            @foreach (request()->only(['q', 'status', 'area', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <label class="sr-only" for="status-{{ $table->id }}">Update status</label>
            <select id="status-{{ $table->id }}" name="status" class="input-field py-2 text-xs" onchange="this.form.submit()">
                @foreach (\App\Enums\TableStatus::cases() as $statusOption)
                    <option value="{{ $statusOption->value }}" @selected($table->status === $statusOption)>
                        {{ $statusOption->label() }}
                    </option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.tables.edit', $table) }}" class="btn-outline px-3 py-2 text-xs">Edit</a>
        <form method="POST" action="{{ route('admin.tables.destroy', $table) }}" onsubmit="return confirm('Delete {{ $table->code }}?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-outline px-3 py-2 text-xs text-red-600 hover:border-red-300 hover:text-red-700">Delete</button>
        </form>
    </div>
</article>
