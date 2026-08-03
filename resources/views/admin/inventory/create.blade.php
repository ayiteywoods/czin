@php
    use App\Enums\StockMovementType;
@endphp

@extends('layouts.admin')

@section('heading', 'Adjust stock')

@section('content')
    <form method="POST" action="{{ route('admin.inventory.store') }}" class="card max-w-2xl space-y-4 p-6">
        @csrf
        <div>
            <x-form-label :required="true">Product</x-form-label>
            <select name="product_id" required class="input-field">
                <option value="">Select product</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected((string) old('product_id') === (string) $product->id)>
                        {{ $product->name }} (stock: {{ $product->quantity }})
                    </option>
                @endforeach
            </select>
            @error('product_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <x-form-label :required="true">Type</x-form-label>
            <select name="type" required class="input-field">
                @foreach (StockMovementType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(old('type', StockMovementType::Adjustment->value) === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
            @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <x-form-label :required="true">Quantity change</x-form-label>
            <input type="number" name="quantity_change" value="{{ old('quantity_change') }}" required class="input-field" placeholder="e.g. 5 or -2">
            <p class="mt-1 text-xs text-brand-muted">Use negative values to decrease stock.</p>
            @error('quantity_change')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Reason</label>
            <input type="text" name="reason" value="{{ old('reason') }}" class="input-field">
            @error('reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary">Save adjustment</button>
    </form>
@endsection
