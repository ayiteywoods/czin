@extends('layouts.admin')

@section('heading', 'Point of Sale')
@section('subheading', 'Take in-store orders and payments')

@section('content')
    <div
        class="admin-pos"
        x-data="adminPos({
            menu: @js($menuPayload),
            categories: @js($categories->map(fn ($category) => ['id' => $category->id, 'name' => $category->name])->values()->all()),
            tables: @js($tables->map(fn ($table) => [
                'id' => $table->id,
                'code' => $table->code,
                'name' => $table->name,
                'area' => $table->area,
                'capacity' => $table->capacity,
                'status' => $table->status->value,
                'status_label' => $table->status->label(),
            ])->values()->all()),
            currencySymbol: @js($currencySymbol),
            taxRate: @js((float) config('shop.tax_rate')),
            storeUrl: @js(route('admin.pos.store')),
            csrf: @js(csrf_token()),
        })"
    >
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-medium">Could not complete the order:</p>
                <ul class="mt-1 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="admin-pos-layout">
            {{-- Menu panel --}}
            <section class="admin-pos-menu card">
                <div class="admin-pos-toolbar">
                    <div class="admin-pos-search-wrap">
                        <svg class="admin-pos-search-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input
                            type="search"
                            class="input-field admin-pos-search"
                            placeholder="Search menu items..."
                            x-model="search"
                        >
                    </div>
                </div>

                <div class="admin-pos-categories">
                    <button
                        type="button"
                        class="admin-pos-category"
                        :class="{ 'admin-pos-category-active': selectedCategory === null }"
                        @click="selectedCategory = null"
                    >
                        All items
                    </button>
                    <template x-for="category in categories" :key="category.id">
                        <button
                            type="button"
                            class="admin-pos-category"
                            :class="{ 'admin-pos-category-active': selectedCategory === category.id }"
                            @click="selectedCategory = category.id"
                            x-text="category.name"
                        ></button>
                    </template>
                </div>

                <div class="admin-pos-products">
                    <template x-if="filteredProducts.length === 0">
                        <div class="admin-pos-empty">
                            <p class="text-sm text-brand-muted">No menu items match your search.</p>
                        </div>
                    </template>

                    <template x-for="product in filteredProducts" :key="product.id">
                        <button
                            type="button"
                            class="admin-pos-product"
                            @click="selectProduct(product)"
                        >
                            <div class="admin-pos-product-image-wrap">
                                <img :src="product.image" :alt="product.name" class="admin-pos-product-image" loading="lazy">
                            </div>
                            <div class="admin-pos-product-body">
                                <p class="admin-pos-product-category" x-text="product.category || 'Menu'"></p>
                                <h3 class="admin-pos-product-name" x-text="product.name"></h3>
                                <p class="admin-pos-product-price">
                                    <span x-text="currencySymbol"></span>
                                    <span x-text="formatMoney(product.price)"></span>
                                </p>
                            </div>
                        </button>
                    </template>
                </div>
            </section>

            {{-- Cart panel --}}
            <aside class="admin-pos-cart card">
                <div class="admin-pos-cart-header">
                    <div>
                        <h2 class="text-lg font-semibold">Current order</h2>
                        <p class="text-sm text-brand-muted" x-text="cart.length ? cart.length + ' line item(s)' : 'Add items from the menu'"></p>
                    </div>
                    <button
                        type="button"
                        class="text-sm font-medium text-brand-red hover:underline"
                        x-show="cart.length > 0"
                        @click="clearCart()"
                    >
                        Clear
                    </button>
                </div>

                <div class="admin-pos-cart-lines">
                    <template x-if="cart.length === 0">
                        <div class="admin-pos-empty">
                            <p class="text-sm text-brand-muted">Cart is empty. Tap a dish to add it.</p>
                        </div>
                    </template>

                    <template x-for="(line, index) in cart" :key="line.key">
                        <div class="admin-pos-cart-line">
                            <div class="min-w-0 flex-1">
                                <p class="font-medium" x-text="line.product_name"></p>
                                <p class="text-xs text-brand-muted" x-text="line.variant_label"></p>
                                <p class="mt-1 text-sm text-brand-red">
                                    <span x-text="currencySymbol"></span>
                                    <span x-text="formatMoney(line.unit_price)"></span>
                                </p>
                            </div>
                            <div class="admin-pos-qty">
                                <button type="button" class="admin-pos-qty-btn" @click="changeQuantity(index, -1)">−</button>
                                <span class="admin-pos-qty-value" x-text="line.quantity"></span>
                                <button type="button" class="admin-pos-qty-btn" @click="changeQuantity(index, 1)">+</button>
                            </div>
                            <button type="button" class="admin-pos-remove" @click="removeLine(index)" aria-label="Remove item">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="admin-pos-options">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-brand-muted">Order type</label>
                        <div class="admin-pos-type-grid">
                            @foreach ($fulfillmentTypes as $type)
                                <button
                                    type="button"
                                    class="admin-pos-type"
                                    :class="{ 'admin-pos-type-active': fulfillmentType === @js($type->value) }"
                                    @click="fulfillmentType = @js($type->value)"
                                >
                                    {{ $type->label() }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="fulfillmentType === 'dine_in'" x-cloak>
                        <label for="pos-table" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-brand-muted">Table</label>
                        <select id="pos-table" class="input-field" x-model="diningTableId">
                            <option value="">Select table</option>
                            <template x-for="table in tables" :key="table.id">
                                <option
                                    :value="table.id"
                                    x-text="table.code + ' · ' + table.name + ' (' + table.area + ') — ' + table.status_label"
                                ></option>
                            </template>
                        </select>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="pos-customer-name" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-brand-muted">Customer name</label>
                            <input id="pos-customer-name" type="text" class="input-field" placeholder="Walk-in customer" x-model="customerName">
                        </div>
                        <div>
                            <label for="pos-customer-phone" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-brand-muted">Phone</label>
                            <input id="pos-customer-phone" type="text" class="input-field" placeholder="Optional" x-model="customerPhone">
                        </div>
                    </div>

                    <div>
                        <label for="pos-comment" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-brand-muted">Kitchen note</label>
                        <textarea id="pos-comment" rows="2" class="input-field" placeholder="Allergies, spice level, etc." x-model="customerComment"></textarea>
                    </div>
                </div>

                <div class="admin-pos-totals">
                    <div class="flex justify-between text-sm">
                        <span class="text-brand-muted">Subtotal</span>
                        <span><span x-text="currencySymbol"></span> <span x-text="formatMoney(subtotal)"></span></span>
                    </div>
                    <div class="flex justify-between text-sm" x-show="tax > 0">
                        <span class="text-brand-muted">Tax</span>
                        <span><span x-text="currencySymbol"></span> <span x-text="formatMoney(tax)"></span></span>
                    </div>
                    <div class="flex justify-between border-t border-neutral-200 pt-3 text-base font-semibold">
                        <span>Total</span>
                        <span class="text-brand-red"><span x-text="currencySymbol"></span> <span x-text="formatMoney(total)"></span></span>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-brand-muted">Payment method</label>
                    <div class="admin-pos-pay-grid">
                        <button type="button" class="admin-pos-pay" :class="{ 'admin-pos-pay-active': paymentMethod === 'cash' }" @click="paymentMethod = 'cash'">Cash</button>
                        <button type="button" class="admin-pos-pay" :class="{ 'admin-pos-pay-active': paymentMethod === 'card' }" @click="paymentMethod = 'card'">Card</button>
                        <button type="button" class="admin-pos-pay" :class="{ 'admin-pos-pay-active': paymentMethod === 'momo' }" @click="paymentMethod = 'momo'">MoMo</button>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn-primary admin-pos-submit"
                    :disabled="submitting || cart.length === 0"
                    @click="submitOrder()"
                >
                    <span x-show="!submitting">Complete order &amp; mark paid</span>
                    <span x-show="submitting" x-cloak>Processing...</span>
                </button>
            </aside>
        </div>

        {{-- Variant picker --}}
        <div
            x-show="variantPicker.open"
            x-cloak
            class="admin-pos-modal-backdrop"
            @click.self="closeVariantPicker()"
        >
            <div class="admin-pos-modal card" @click.stop>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-muted">Choose option</p>
                        <h3 class="mt-1 text-lg font-semibold" x-text="variantPicker.product?.name"></h3>
                    </div>
                    <button type="button" class="admin-pos-remove" @click="closeVariantPicker()" aria-label="Close">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-4 space-y-2">
                    <template x-for="variant in variantPicker.product?.variants || []" :key="variant.id">
                        <button
                            type="button"
                            class="admin-pos-variant"
                            @click="addVariant(variantPicker.product, variant)"
                        >
                            <span x-text="variant.label"></span>
                            <span class="font-semibold text-brand-red">
                                <span x-text="currencySymbol"></span>
                                <span x-text="formatMoney(variant.price)"></span>
                            </span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminPos', (config) => ({
                menu: config.menu,
                categories: config.categories,
                tables: config.tables,
                currencySymbol: config.currencySymbol,
                taxRate: config.taxRate,
                storeUrl: config.storeUrl,
                csrf: config.csrf,
                search: '',
                selectedCategory: null,
                fulfillmentType: 'dine_in',
                diningTableId: '',
                customerName: '',
                customerPhone: '',
                customerComment: '',
                paymentMethod: 'cash',
                cart: [],
                submitting: false,
                variantPicker: {
                    open: false,
                    product: null,
                },

                get filteredProducts() {
                    const query = this.search.trim().toLowerCase();

                    return this.menu.filter((product) => {
                        if (this.selectedCategory !== null && product.category_id !== this.selectedCategory) {
                            return false;
                        }

                        if (!query) {
                            return true;
                        }

                        return product.name.toLowerCase().includes(query)
                            || (product.category || '').toLowerCase().includes(query);
                    });
                },

                get subtotal() {
                    return this.cart.reduce((sum, line) => sum + (line.unit_price * line.quantity), 0);
                },

                get tax() {
                    return Math.round(Math.max(0, this.subtotal) * this.taxRate * 100) / 100;
                },

                get total() {
                    return Math.round((this.subtotal + this.tax) * 100) / 100;
                },

                formatMoney(value) {
                    return Number(value || 0).toFixed(2);
                },

                selectProduct(product) {
                    if (!product.variants?.length) {
                        return;
                    }

                    if (product.variants.length === 1) {
                        this.addVariant(product, product.variants[0]);
                        return;
                    }

                    this.variantPicker = { open: true, product };
                },

                closeVariantPicker() {
                    this.variantPicker = { open: false, product: null };
                },

                addVariant(product, variant) {
                    const existing = this.cart.find((line) => line.product_variant_id === variant.id);

                    if (existing) {
                        existing.quantity += 1;
                    } else {
                        this.cart.push({
                            key: `${variant.id}-${Date.now()}`,
                            product_variant_id: variant.id,
                            product_name: product.name,
                            variant_label: variant.label,
                            unit_price: variant.price,
                            quantity: 1,
                        });
                    }

                    this.closeVariantPicker();
                },

                changeQuantity(index, delta) {
                    const line = this.cart[index];
                    if (!line) {
                        return;
                    }

                    line.quantity += delta;

                    if (line.quantity <= 0) {
                        this.cart.splice(index, 1);
                    }
                },

                removeLine(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    this.cart = [];
                },

                submitOrder() {
                    if (this.submitting || this.cart.length === 0) {
                        return;
                    }

                    this.submitting = true;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = this.storeUrl;

                    const addField = (name, value) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = value;
                        form.appendChild(input);
                    };

                    addField('_token', this.csrf);
                    addField('fulfillment_type', this.fulfillmentType);
                    addField('payment_method', this.paymentMethod);
                    addField('customer_name', this.customerName);
                    addField('customer_phone', this.customerPhone);
                    addField('customer_comment', this.customerComment);

                    if (this.fulfillmentType === 'dine_in' && this.diningTableId) {
                        addField('dining_table_id', this.diningTableId);
                    }

                    this.cart.forEach((line, index) => {
                        addField(`items[${index}][product_variant_id]`, line.product_variant_id);
                        addField(`items[${index}][quantity]`, line.quantity);
                    });

                    document.body.appendChild(form);
                    form.submit();
                },
            }));
        });
    </script>
@endpush
