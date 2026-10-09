@php($product = $product ?? null)

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-form-label :required="true">Dish name</x-form-label>
        <input type="text" name="name" value="{{ old('name', $product?->name) }}" required class="input-field" placeholder="e.g. Jollof rice with chicken">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <x-form-label :required="true">Item code</x-form-label>
        <input type="text" name="sku" value="{{ old('sku', $product?->sku) }}" required class="input-field" placeholder="e.g. JOLLOF-01">
        <p class="mt-1 text-xs text-brand-muted">Short code for kitchen tickets, POS, and stock tracking.</p>
        @error('sku')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <x-form-label :required="true">Menu category</x-form-label>
        <select name="category_id" required class="input-field">
            <option value="">Select category</option>
            @include('partials.category-select-options', [
                'categoryTree' => $categoryTree,
                'selected' => old('category_id', $product?->category_id),
            ])
        </select>
        @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <x-form-label :required="true">Price (GHS)</x-form-label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $product?->price) }}" required class="input-field">
        @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Special price (GHS)</label>
        <input type="number" step="0.01" name="discount_price" value="{{ old('discount_price', $product?->discount_price) }}" class="input-field">
        <p class="mt-1 text-xs text-brand-muted">Optional promo or lunch special price.</p>
        @error('discount_price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Portions available</label>
        <input
            type="number"
            value="{{ old('quantity', $product?->quantity ?? 0) }}"
            readonly
            class="input-field bg-neutral-50"
        >
        <p class="mt-1 text-xs text-brand-muted">Calculated automatically from the portion and option rows below.</p>
    </div>

    <div>
        <x-form-label :required="true">Menu status</x-form-label>
        <select name="status" class="input-field">
            @foreach (\App\Enums\ProductStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', $product?->status?->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2 rounded border border-neutral-200 bg-neutral-50 p-4">
        <h3 class="text-sm font-semibold">When this item appears on the menu</h3>
        <p class="mt-1 text-xs text-brand-muted">
            Optional. The dish appears online and for ordering when status is Active and this date and time have passed.
            Leave blank to make it available immediately.
        </p>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="publish_date" class="block text-sm font-medium">Available from (date)</label>
                <input
                    id="publish_date"
                    type="date"
                    name="publish_date"
                    value="{{ old('publish_date', $product?->published_at?->timezone(config('app.timezone'))->format('Y-m-d')) }}"
                    class="input-field mt-1"
                >
                @error('publish_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="publish_time" class="block text-sm font-medium">Available from (time)</label>
                <input
                    id="publish_time"
                    type="time"
                    name="publish_time"
                    value="{{ old('publish_time', $product?->published_at?->timezone(config('app.timezone'))->format('H:i')) }}"
                    class="input-field mt-1"
                >
                @error('publish_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        @error('published_at')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        <p class="mt-2 text-xs text-brand-muted">Times use {{ config('app.timezone') }}.</p>
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium">Description</label>
        <textarea name="description" rows="5" class="input-field" placeholder="Short description for the menu — ingredients, spice level, or serving notes.">{{ old('description', $product?->description) }}</textarea>
        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium">Food photos</label>
        <input type="file" name="images[]" accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,image/avif,image/heic,image/heif,.jpg,.jpeg,.png,.gif,.webp,.bmp,.avif,.heic,.heif" multiple class="mt-1 w-full text-sm">
        <p class="mt-1 text-xs text-brand-muted">Upload JPG, PNG, GIF, WebP, AVIF, or HEIC. Large images are automatically compressed to {{ \App\Support\ImageUpload::targetLabel(4096) }} each.</p>
        @if ($product?->images?->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-3">
                @foreach ($product->images as $image)
                    <img src="{{ asset('storage/'.$image->path) }}" alt="" class="h-20 w-20 rounded-lg object-cover">
                @endforeach
            </div>
        @endif
        @error('images')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('images.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2" x-data="productVariantForm(@js(old('variants', $product?->variants?->map(fn ($variant) => [
        'id' => $variant->id,
        'size' => $variant->size,
        'color' => $variant->color,
        'heel_length' => $variant->heel_length,
        'quantity' => $variant->quantity,
        'sku' => $variant->sku,
        'is_active' => $variant->is_active,
    ])->values()->all() ?? [[
        'size' => '',
        'color' => '',
        'heel_length' => '',
        'quantity' => 0,
        'sku' => '',
        'is_active' => true,
    ]])))">
        <div class="flex items-center justify-between gap-3">
            <div>
                <label class="block text-sm font-medium">Portions & options<span class="text-brand-red" aria-hidden="true"> *</span></label>
                <p class="mt-1 text-xs text-brand-muted">Add the choices guests can order — portion size, spice/style, and optional extras. Names appear on the menu as you type them.</p>
            </div>
            <button type="button" class="btn-outline px-3 py-2 text-xs" @click="addRow()">Add option</button>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-neutral-200 text-left text-xs uppercase tracking-wide text-brand-muted">
                    <tr>
                        <th class="px-2 py-2">Portion <span class="text-brand-red">*</span></th>
                        <th class="px-2 py-2">Option <span class="text-brand-red">*</span></th>
                        <th class="px-2 py-2">Extra <span class="normal-case text-brand-muted">(optional)</span></th>
                        <th class="px-2 py-2">Qty <span class="text-brand-red">*</span></th>
                        <th class="px-2 py-2">Item code</th>
                        <th class="px-2 py-2">On menu</th>
                        <th class="px-2 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in rows" :key="index">
                        <tr class="border-b border-neutral-100">
                            <td class="px-2 py-2">
                                <input type="hidden" :name="`variants[${index}][id]`" :value="row.id || ''">
                                <input type="text" :name="`variants[${index}][size]`" x-model="row.size" list="product-sizes" required placeholder="e.g. Regular" class="input-field min-w-[5rem]">
                            </td>
                            <td class="px-2 py-2">
                                <input
                                    type="text"
                                    :name="`variants[${index}][color]`"
                                    x-model="row.color"
                                    list="product-options"
                                    autocomplete="off"
                                    placeholder="e.g. Spicy"
                                    required
                                    class="input-field min-w-[6rem]"
                                >
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" :name="`variants[${index}][heel_length]`" x-model="row.heel_length" list="product-extras" placeholder="e.g. Extra meat" class="input-field min-w-[6rem]">
                            </td>
                            <td class="px-2 py-2">
                                <input type="number" :name="`variants[${index}][quantity]`" x-model="row.quantity" min="0" required class="input-field w-20">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" :name="`variants[${index}][sku]`" x-model="row.sku" placeholder="Auto" class="input-field min-w-[8rem]">
                            </td>
                            <td class="px-2 py-2 text-center">
                                <input type="hidden" :name="`variants[${index}][is_active]`" :value="row.is_active ? 1 : 0">
                                <input type="checkbox" x-model="row.is_active" class="rounded-none border-neutral-300 text-brand-red focus:ring-brand-red">
                            </td>
                            <td class="px-2 py-2">
                                <button type="button" class="text-xs text-brand-red hover:underline" @click="removeRow(index)" x-show="rows.length > 1">Remove</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <datalist id="product-sizes">
            @foreach (config('shop.product_sizes') as $size)
                <option value="{{ $size }}"></option>
            @endforeach
        </datalist>
        <datalist id="product-options">
            @foreach (config('shop.product_colors') as $option)
                <option value="{{ $option }}"></option>
            @endforeach
        </datalist>
        <datalist id="product-extras">
            @foreach (config('shop.product_extras', config('shop.product_heel_lengths', [])) as $extra)
                <option value="{{ $extra }}"></option>
            @endforeach
        </datalist>

        @error('variants')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('variants.*')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('productVariantForm', (initialRows = []) => ({
                    rows: initialRows.length ? initialRows : [{
                        size: '',
                        color: '',
                        heel_length: '',
                        quantity: 0,
                        sku: '',
                        is_active: true,
                    }],
                    addRow() {
                        this.rows.push({
                            size: '',
                            color: '',
                            heel_length: '',
                            quantity: 0,
                            sku: '',
                            is_active: true,
                        });
                    },
                    removeRow(index) {
                        if (this.rows.length > 1) {
                            this.rows.splice(index, 1);
                        }
                    },
                }));
            });
        </script>
    @endpush
@endonce
