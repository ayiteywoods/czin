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
            taxRate: @js(\App\Support\ShopTax::rate()),
            taxLabel: @js(\App\Support\ShopTax::label()),
            storeUrl: @js(route('admin.pos.store')),
            readyPollUrl: @js($readyPollUrl),
            initialReadyOrders: @js($initialReadyOrders),
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

        <div
            x-show="readyAlertBanner"
            x-cloak
            class="admin-pos-ready-banner"
            x-text="readyAlertBanner"
        ></div>

        <div class="admin-pos-ready-toolbar card mb-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div>
                        <p class="text-sm font-semibold">Kitchen ready queue</p>
                        <p class="text-xs text-brand-muted">
                            <span x-text="readyOrders.length"></span> meal(s) waiting · updates every few seconds
                        </p>
                    </div>
                    <span
                        class="admin-pos-ready-badge"
                        x-show="readyOrders.length > 0"
                        x-cloak
                        x-text="readyOrders.length"
                    ></span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        class="admin-kitchen-toggle"
                        :class="{ 'admin-kitchen-toggle-active': soundEnabled }"
                        @click="toggleSound()"
                    >
                        <span x-text="soundEnabled ? 'Sound on' : 'Sound off'"></span>
                    </button>
                    <button type="button" class="admin-kitchen-toggle" @click="testReadyAlert()">
                        Test alert
                    </button>
                </div>
            </div>

            <div class="mt-4 space-y-3" x-show="readyOrders.length > 0" x-cloak>
                <template x-for="order in readyOrders" :key="order.id">
                    <div class="admin-pos-ready-card" :class="{ 'admin-pos-ready-card-new': isNewReady(order.id) }">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold" x-text="'#' + order.order_number"></p>
                                <span class="admin-pos-ready-pill" x-text="order.fulfillment_label"></span>
                                <span class="text-xs text-brand-muted" x-text="'Ready ' + (order.ready_for || '')"></span>
                            </div>
                            <p class="mt-1 text-sm text-brand-muted" x-show="order.table">
                                Table <span x-text="order.table?.code"></span> · <span x-text="order.table?.name"></span>
                            </p>
                            <p class="mt-1 text-sm" x-show="order.customer_name && order.customer_name !== 'Walk-in Customer'">
                                <span x-text="order.customer_name"></span>
                            </p>
                            <ul class="mt-2 space-y-0.5 text-sm">
                                <template x-for="(item, idx) in order.items" :key="idx">
                                    <li>
                                        <span class="font-medium" x-text="item.quantity + '× '"></span>
                                        <span x-text="item.name"></span>
                                        <span class="text-brand-muted" x-show="item.options" x-text="' (' + item.options + ')'"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <div class="flex shrink-0 flex-col gap-2">
                            <button
                                type="button"
                                class="btn-primary px-3 py-2 text-sm"
                                :disabled="servingOrderId === order.id"
                                @click="markServed(order)"
                            >
                                <span x-show="servingOrderId !== order.id">Mark served</span>
                                <span x-show="servingOrderId === order.id" x-cloak>Saving...</span>
                            </button>
                            <a :href="order.show_url" class="text-center text-xs font-medium text-brand-red hover:underline">View order</a>
                        </div>
                    </div>
                </template>
            </div>

            <p class="mt-3 text-sm text-brand-muted" x-show="readyOrders.length === 0">
                No meals waiting. When kitchen marks an order ready, it will show here with sound and flash.
            </p>
        </div>

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
                        <span class="text-brand-muted" x-text="taxLabel || 'Tax'"></span>
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
                taxLabel: config.taxLabel || 'Tax',
                storeUrl: config.storeUrl,
                readyPollUrl: config.readyPollUrl,
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
                readyOrders: config.initialReadyOrders || [],
                knownReadyIds: new Set(),
                newReadyIds: new Set(),
                soundEnabled: true,
                readyAlertBanner: '',
                servingOrderId: null,
                readyPolling: false,
                readyPollTimer: null,
                readyInitialized: false,

                init() {
                    this.loadReadyPreferences();
                    (this.readyOrders || []).forEach((order) => this.knownReadyIds.add(order.id));
                    this.readyInitialized = true;
                    this.readyPollTimer = setInterval(() => this.pollReadyOrders(), 5000);
                },

                destroy() {
                    if (this.readyPollTimer) {
                        clearInterval(this.readyPollTimer);
                    }
                },

                loadReadyPreferences() {
                    this.soundEnabled = localStorage.getItem('pos-ready-sound-enabled') !== 'false';
                },

                toggleSound() {
                    this.soundEnabled = !this.soundEnabled;
                    localStorage.setItem('pos-ready-sound-enabled', this.soundEnabled ? 'true' : 'false');
                    if (this.soundEnabled) {
                        this.maybeRequestDesktopNotifications();
                    }
                },

                testReadyAlert() {
                    this.maybeRequestDesktopNotifications();
                    this.playReadySound();
                    this.flashScreen();
                    this.readyAlertBanner = 'Test alert — sound and flash are working.';
                    setTimeout(() => {
                        this.readyAlertBanner = '';
                    }, 4000);
                },

                isNewReady(orderId) {
                    return this.newReadyIds.has(orderId);
                },

                handleNewReadyOrders(orders) {
                    if (!orders?.length) {
                        return;
                    }

                    const labels = orders.map((order) => `#${order.order_number}`).join(', ');
                    this.readyAlertBanner = orders.length === 1
                        ? `Meal ready: ${labels}`
                        : `${orders.length} meals ready: ${labels}`;

                    if (this.soundEnabled) {
                        this.playReadySound();
                    }

                    this.flashScreen();
                    this.pulseDocumentTitle(this.readyAlertBanner);

                    if (typeof document !== 'undefined' && 'Notification' in window && Notification.permission === 'granted') {
                        try {
                            new Notification('Meal ready to serve', {
                                body: this.readyAlertBanner,
                                tag: 'pos-ready-orders',
                            });
                        } catch (error) {
                            // Ignore notification failures.
                        }
                    }

                    setTimeout(() => {
                        this.readyAlertBanner = '';
                    }, 8000);

                    setTimeout(() => {
                        orders.forEach((order) => this.newReadyIds.delete(order.id));
                    }, 20000);
                },

                pulseDocumentTitle(message) {
                    if (typeof document === 'undefined' || document.hasFocus()) {
                        return;
                    }

                    const original = document.title;
                    let ticks = 0;

                    if (this._titlePulseTimer) {
                        clearInterval(this._titlePulseTimer);
                        document.title = this._originalTitle || original;
                    }

                    this._originalTitle = original;
                    this._titlePulseTimer = setInterval(() => {
                        document.title = ticks % 2 === 0 ? `READY: ${message}` : original;
                        ticks += 1;

                        if (ticks >= 12 || document.hasFocus()) {
                            clearInterval(this._titlePulseTimer);
                            this._titlePulseTimer = null;
                            document.title = original;
                        }
                    }, 900);
                },

                playReadySound() {
                    try {
                        const context = new (window.AudioContext || window.webkitAudioContext)();
                        [0, 0.2, 0.4].forEach((delay, index) => {
                            const oscillator = context.createOscillator();
                            const gain = context.createGain();
                            oscillator.type = 'square';
                            oscillator.frequency.value = index === 1 ? 980 : 760;
                            gain.gain.value = 0.09;
                            oscillator.connect(gain);
                            gain.connect(context.destination);
                            oscillator.start(context.currentTime + delay);
                            oscillator.stop(context.currentTime + delay + 0.16);
                        });
                    } catch (error) {
                        // Ignore browsers that block audio without interaction.
                    }
                },

                flashScreen() {
                    const overlay = document.createElement('div');
                    overlay.className = 'admin-kitchen-flash-overlay';
                    document.body.appendChild(overlay);
                    setTimeout(() => overlay.remove(), 500);
                },

                detectNewReadyOrders(orders) {
                    const incomingIds = orders.map((order) => order.id);
                    const fresh = orders.filter((order) => !this.knownReadyIds.has(order.id));

                    incomingIds.forEach((id) => this.knownReadyIds.add(id));

                    // Drop ids that are no longer ready so re-ready can alert again later.
                    [...this.knownReadyIds].forEach((id) => {
                        if (!incomingIds.includes(id)) {
                            this.knownReadyIds.delete(id);
                            this.newReadyIds.delete(id);
                        }
                    });

                    if (!this.readyInitialized || fresh.length === 0) {
                        return [];
                    }

                    fresh.forEach((order) => this.newReadyIds.add(order.id));

                    return fresh;
                },

                async pollReadyOrders() {
                    if (!this.readyPollUrl || this.readyPolling) {
                        return;
                    }

                    this.readyPolling = true;

                    try {
                        const response = await fetch(this.readyPollUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Failed to refresh ready orders.');
                        }

                        const data = await response.json();
                        const orders = data.orders || [];
                        const fresh = this.detectNewReadyOrders(orders);

                        this.readyOrders = orders;

                        if (fresh.length > 0) {
                            this.handleNewReadyOrders(fresh);
                        }
                    } catch (error) {
                        // Keep POS usable if polling fails briefly.
                    } finally {
                        this.readyPolling = false;
                    }
                },

                async markServed(order) {
                    if (!order?.served_url || this.servingOrderId === order.id) {
                        return;
                    }

                    this.servingOrderId = order.id;

                    try {
                        const response = await fetch(order.served_url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': this.csrf,
                            },
                            body: JSON.stringify({}),
                        });

                        if (!response.ok) {
                            throw new Error('Failed to mark order served.');
                        }

                        const data = await response.json();
                        const orders = data.orders || [];

                        this.detectNewReadyOrders(orders);
                        this.readyOrders = orders;
                        this.newReadyIds.delete(order.id);
                        this.knownReadyIds.delete(order.id);
                    } catch (error) {
                        this.readyAlertBanner = 'Could not mark order as served. Try again.';
                        setTimeout(() => {
                            this.readyAlertBanner = '';
                        }, 5000);
                    } finally {
                        this.servingOrderId = null;
                    }
                },

                maybeRequestDesktopNotifications() {
                    if (!('Notification' in window) || Notification.permission !== 'default') {
                        return;
                    }

                    Notification.requestPermission().catch(() => {});
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
