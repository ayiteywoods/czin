@extends('layouts.admin')

@section('heading', 'Kitchen')
@section('subheading', 'Track food orders and update preparation status')

@section('content')
    <div
        class="admin-kitchen"
        x-data="adminKitchenBoard({
            pollUrl: @js($pollUrl),
            csrf: @js(csrf_token()),
            initial: @js([
                'stats' => $stats,
                'columns' => $columns,
            ]),
        })"
        x-init="init()"
    >
        <div class="admin-kitchen-toolbar">
            <button
                type="button"
                class="admin-kitchen-toggle"
                :class="{ 'admin-kitchen-toggle-active': soundEnabled }"
                @click="toggleSound()"
            >
                Sound alerts
            </button>
            <button
                type="button"
                class="admin-kitchen-toggle"
                :class="{ 'admin-kitchen-toggle-active': autoPrintEnabled }"
                @click="toggleAutoPrint()"
            >
                Auto-print tickets
            </button>
            <button type="button" class="admin-kitchen-toggle" @click="testAlert()">
                Test alert
            </button>
            <p class="text-xs text-brand-muted">SMS/WhatsApp alerts are configured in Store settings. This board handles sound, flash, and printing.</p>
        </div>

        <template x-if="alertBanner">
            <div class="admin-kitchen-alert-banner" x-text="alertBanner"></div>
        </template>
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="grid flex-1 grid-cols-2 gap-3 lg:grid-cols-4">
                <div class="admin-table-stat admin-table-stat-primary">
                    <p class="admin-table-stat-label">Active tickets</p>
                    <p class="admin-table-stat-value" x-text="stats.total">{{ $stats['total'] }}</p>
                </div>
                <div class="admin-table-stat">
                    <p class="admin-table-stat-label">New</p>
                    <p class="admin-table-stat-value text-blue-600" x-text="stats.new">{{ $stats['new'] }}</p>
                </div>
                <div class="admin-table-stat">
                    <p class="admin-table-stat-label">Preparing</p>
                    <p class="admin-table-stat-value text-amber-600" x-text="stats.preparing">{{ $stats['preparing'] }}</p>
                </div>
                <div class="admin-table-stat">
                    <p class="admin-table-stat-label">Ready</p>
                    <p class="admin-table-stat-value text-green-600" x-text="stats.ready">{{ $stats['ready'] }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <p class="text-xs text-brand-muted" x-show="lastUpdated" x-cloak>
                    Updated <span x-text="lastUpdated"></span>
                </p>
                <button type="button" class="btn-outline px-4 py-2.5" @click="refresh()" :disabled="loading">
                    <span x-show="!loading">Refresh</span>
                    <span x-show="loading" x-cloak>Refreshing...</span>
                </button>
            </div>
        </div>

        <template x-if="flashMessage">
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" x-text="flashMessage"></div>
        </template>

        <div class="admin-kitchen-board">
            <section class="admin-kitchen-column">
                <header class="admin-kitchen-column-header admin-kitchen-column-header-new">
                    <h2>New orders</h2>
                    <span class="admin-kitchen-count" x-text="stats.new">{{ $stats['new'] }}</span>
                </header>
                <div class="admin-kitchen-column-body" id="kitchen-column-new">
                    @foreach ($columns['new'] as $order)
                        <x-admin-kitchen-order-card :order="$order" />
                    @endforeach
                    <div class="admin-kitchen-empty" x-show="columns.new.length === 0" @if ($columns['new'] !== []) x-cloak @endif>
                        <p>No new tickets</p>
                    </div>
                </div>
            </section>

            <section class="admin-kitchen-column">
                <header class="admin-kitchen-column-header admin-kitchen-column-header-preparing">
                    <h2>Preparing</h2>
                    <span class="admin-kitchen-count" x-text="stats.preparing">{{ $stats['preparing'] }}</span>
                </header>
                <div class="admin-kitchen-column-body" id="kitchen-column-preparing">
                    @foreach ($columns['preparing'] as $order)
                        <x-admin-kitchen-order-card :order="$order" />
                    @endforeach
                    <div class="admin-kitchen-empty" x-show="columns.preparing.length === 0" @if ($columns['preparing'] !== []) x-cloak @endif>
                        <p>Nothing cooking right now</p>
                    </div>
                </div>
            </section>

            <section class="admin-kitchen-column">
                <header class="admin-kitchen-column-header admin-kitchen-column-header-ready">
                    <h2>Ready</h2>
                    <span class="admin-kitchen-count" x-text="stats.ready">{{ $stats['ready'] }}</span>
                </header>
                <div class="admin-kitchen-column-body" id="kitchen-column-ready">
                    @foreach ($columns['ready'] as $order)
                        <x-admin-kitchen-order-card :order="$order" />
                    @endforeach
                    <div class="admin-kitchen-empty" x-show="columns.ready.length === 0" @if ($columns['ready'] !== []) x-cloak @endif>
                        <p>No orders waiting</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminKitchenBoard', (config) => ({
                pollUrl: config.pollUrl,
                csrf: config.csrf,
                stats: config.initial.stats,
                columns: config.initial.columns,
                loading: false,
                flashMessage: '',
                alertBanner: '',
                lastUpdated: 'just now',
                pollTimer: null,
                knownOrderIds: new Set(),
                newOrderIds: new Set(),
                printedOrderIds: new Set(),
                soundEnabled: true,
                autoPrintEnabled: true,
                initialized: false,

                init() {
                    this.loadPreferences();
                    this.seedKnownOrders();
                    this.bindForms();
                    this.pollTimer = setInterval(() => this.refresh(true), 15000);
                },

                loadPreferences() {
                    this.soundEnabled = localStorage.getItem('kitchen-sound-enabled') !== 'false';
                    this.autoPrintEnabled = localStorage.getItem('kitchen-auto-print-enabled') !== 'false';

                    try {
                        const printed = JSON.parse(localStorage.getItem('kitchen-printed-orders') || '[]');
                        this.printedOrderIds = new Set(printed);
                    } catch (error) {
                        this.printedOrderIds = new Set();
                    }
                },

                seedKnownOrders() {
                    this.collectOrderIds(this.columns).forEach((id) => this.knownOrderIds.add(id));
                    this.initialized = true;
                },

                collectOrderIds(columns) {
                    return ['new', 'preparing', 'ready'].flatMap((column) => (
                        (columns[column] || []).map((order) => order.id)
                    ));
                },

                toggleSound() {
                    this.soundEnabled = !this.soundEnabled;
                    localStorage.setItem('kitchen-sound-enabled', this.soundEnabled ? 'true' : 'false');
                },

                toggleAutoPrint() {
                    this.autoPrintEnabled = !this.autoPrintEnabled;
                    localStorage.setItem('kitchen-auto-print-enabled', this.autoPrintEnabled ? 'true' : 'false');
                },

                testAlert() {
                    this.playAlertSound();
                    this.flashScreen();
                    this.alertBanner = 'Test alert — sound and flash are working.';
                    setTimeout(() => {
                        this.alertBanner = '';
                    }, 4000);
                },

                handleNewOrders(orderIds) {
                    if (!orderIds?.length) {
                        return;
                    }

                    this.alertBanner = `${orderIds.length} new kitchen order${orderIds.length === 1 ? '' : 's'} received.`;

                    if (this.soundEnabled) {
                        this.playAlertSound();
                    }

                    this.flashScreen();

                    setTimeout(() => {
                        this.alertBanner = '';
                    }, 6000);
                },

                playAlertSound() {
                    try {
                        const context = new (window.AudioContext || window.webkitAudioContext)();
                        [0, 0.18, 0.36].forEach((delay) => {
                            const oscillator = context.createOscillator();
                            const gain = context.createGain();
                            oscillator.type = 'square';
                            oscillator.frequency.value = 880;
                            gain.gain.value = 0.08;
                            oscillator.connect(gain);
                            gain.connect(context.destination);
                            oscillator.start(context.currentTime + delay);
                            oscillator.stop(context.currentTime + delay + 0.14);
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

                detectNewOrders(columns) {
                    const incoming = this.collectOrderIds(columns);
                    const fresh = incoming.filter((id) => !this.knownOrderIds.has(id));

                    incoming.forEach((id) => this.knownOrderIds.add(id));

                    if (!this.initialized || fresh.length === 0) {
                        return [];
                    }

                    fresh.forEach((id) => this.newOrderIds.add(id));

                    return fresh;
                },

                maybePrintOrders(columns, orderIds) {
                    if (!this.autoPrintEnabled || orderIds.length === 0) {
                        return;
                    }

                    const orders = ['new', 'preparing', 'ready']
                        .flatMap((column) => columns[column] || [])
                        .filter((order) => orderIds.includes(order.id));

                    orders.forEach((order) => this.printTicket(order));
                },

                printTicket(order) {
                    if (!order?.print_url || this.printedOrderIds.has(order.id)) {
                        return;
                    }

                    const iframe = document.createElement('iframe');
                    iframe.style.position = 'fixed';
                    iframe.style.width = '0';
                    iframe.style.height = '0';
                    iframe.style.border = '0';
                    iframe.style.opacity = '0';
                    iframe.src = order.print_url;
                    document.body.appendChild(iframe);

                    iframe.onload = () => {
                        try {
                            iframe.contentWindow?.focus();
                            iframe.contentWindow?.print();
                        } catch (error) {
                            window.open(order.print_url, '_blank', 'noopener');
                        }

                        setTimeout(() => iframe.remove(), 3000);
                    };

                    this.printedOrderIds.add(order.id);
                    localStorage.setItem(
                        'kitchen-printed-orders',
                        JSON.stringify([...this.printedOrderIds].slice(-200)),
                    );
                },

                bindForms() {
                    document.querySelectorAll('[data-kitchen-form]').forEach((form) => {
                        if (form.dataset.bound === 'true') {
                            return;
                        }

                        form.dataset.bound = 'true';
                        form.addEventListener('submit', (event) => this.submitStatus(event));
                    });
                },

                async refresh(silent = false) {
                    if (this.loading) {
                        return;
                    }

                    this.loading = true;

                    try {
                        const response = await fetch(this.pollUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Failed to refresh kitchen board.');
                        }

                        const data = await response.json();
                        const newOrderIds = this.detectNewOrders(data.columns);

                        this.stats = data.stats;
                        this.columns = data.columns;
                        this.renderColumns(newOrderIds);
                        this.lastUpdated = 'just now';

                        if (newOrderIds.length > 0) {
                            this.handleNewOrders(newOrderIds);
                            this.maybePrintOrders(data.columns, newOrderIds);
                        }

                        if (!silent) {
                            this.flashMessage = '';
                        }
                    } catch (error) {
                        if (!silent) {
                            this.flashMessage = 'Could not refresh the kitchen board.';
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                renderColumns(highlightIds = []) {
                    ['new', 'preparing', 'ready'].forEach((column) => {
                        const container = document.getElementById(`kitchen-column-${column}`);
                        if (!container) {
                            return;
                        }

                        const orders = this.columns[column] || [];
                        const emptyState = container.querySelector('.admin-kitchen-empty');
                        container.querySelectorAll('.admin-kitchen-card').forEach((card) => card.remove());

                        orders.forEach((order) => {
                            const card = this.createCard(order);
                            if (highlightIds.includes(order.id)) {
                                card.classList.add('admin-kitchen-card--new');
                            }
                            container.insertBefore(card, emptyState);
                        });

                        if (emptyState) {
                            emptyState.style.display = orders.length === 0 ? 'flex' : 'none';
                        }
                    });

                    this.bindForms();
                },

                createCard(order) {
                    const article = document.createElement('article');
                    article.className = 'admin-kitchen-card';
                    article.dataset.orderId = order.id;

                    const itemsHtml = (order.items || []).map((item) => {
                        const options = item.options
                            ? Object.entries(item.options)
                                .filter(([, value]) => value)
                                .map(([key, value]) => `${key.replace(/_/g, ' ')}: ${value}`)
                                .join(', ')
                            : '';

                        const imageHtml = item.image_url
                            ? `<img src="${this.escapeHtml(item.image_url)}" alt="${this.escapeHtml(item.name)}" class="admin-kitchen-item-image" loading="lazy">`
                            : '';

                        return `
                            <li class="admin-kitchen-item">
                                ${imageHtml}
                                <span class="admin-kitchen-item-qty">${item.quantity}×</span>
                                <div class="min-w-0 flex-1">
                                    <p class="admin-kitchen-item-name">${this.escapeHtml(item.name)}</p>
                                    ${options ? `<p class="admin-kitchen-item-options">${this.escapeHtml(options)}</p>` : ''}
                                </div>
                            </li>
                        `;
                    }).join('');

                    const tableHtml = order.table
                        ? `<span class="font-semibold text-brand-black">${this.escapeHtml(order.table.code)} · ${this.escapeHtml(order.table.name)}</span>`
                        : '';

                    const customerHtml = order.customer_name && order.customer_name !== 'Walk-in Customer'
                        ? `<p class="admin-kitchen-card-customer">${this.escapeHtml(order.customer_name)}</p>`
                        : '';

                    const noteHtml = order.customer_comment
                        ? `<div class="admin-kitchen-note">
                                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Kitchen note</p>
                                <p class="mt-1 text-sm text-amber-900">${this.escapeHtml(order.customer_comment)}</p>
                           </div>`
                        : '';

                    const printHtml = order.print_url
                        ? `<div class="admin-kitchen-card-actions"><a href="${order.print_url}" target="_blank" rel="noopener" class="admin-kitchen-print-link">Print ticket</a></div>`
                        : '';

                    const actionHtml = order.next_action
                        ? `<form method="POST" action="${order.update_url}" class="admin-kitchen-action-form" data-kitchen-form>
                                <input type="hidden" name="_token" value="${this.csrf}">
                                <input type="hidden" name="_method" value="PATCH">
                                <button type="submit" class="admin-kitchen-action" data-kitchen-submit>${this.escapeHtml(order.next_action.label)}</button>
                           </form>`
                        : (order.status === 'ready_for_delivery'
                            ? '<p class="admin-kitchen-waiting">Waiting for pickup / service</p>'
                            : '');

                    article.innerHTML = `
                        <div class="admin-kitchen-card-header">
                            <div>
                                <p class="admin-kitchen-card-number">#${this.escapeHtml(order.order_number)}</p>
                                <p class="admin-kitchen-card-time">${this.escapeHtml(order.created_at || '')}</p>
                            </div>
                            <span class="admin-kitchen-pill admin-kitchen-pill-${order.status}">
                                ${this.escapeHtml(order.status_label)}
                            </span>
                        </div>
                        <div class="admin-kitchen-card-meta">
                            <span>${this.escapeHtml(order.fulfillment_label || '')}</span>
                            <span>${this.escapeHtml(order.source_label || '')}</span>
                            ${tableHtml}
                        </div>
                        ${customerHtml}
                        <ul class="admin-kitchen-items">${itemsHtml}</ul>
                        ${noteHtml}
                        ${printHtml}
                        ${actionHtml}
                    `;

                    return article;
                },

                async submitStatus(event) {
                    event.preventDefault();

                    const form = event.currentTarget;
                    const button = form.querySelector('[data-kitchen-submit]');

                    if (button) {
                        button.disabled = true;
                    }

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new FormData(form),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Could not update order status.');
                        }

                        this.stats = data.board.stats;
                        this.columns = data.board.columns;
                        this.renderColumns();
                        this.flashMessage = data.message;
                        this.lastUpdated = 'just now';
                    } catch (error) {
                        this.flashMessage = error.message || 'Could not update order status.';
                    } finally {
                        if (button) {
                            button.disabled = false;
                        }
                    }
                },

                escapeHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                },
            }));
        });
    </script>
@endpush
