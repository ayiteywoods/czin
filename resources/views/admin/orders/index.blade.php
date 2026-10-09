@extends('layouts.admin')

@section('heading', 'Orders')

@section('content')
    @php
        use App\Enums\FulfillmentType;
        use App\Enums\OrderSource;
        use App\Enums\OrderStatus;
        use App\Enums\PaymentStatus;

        $filterOptions = [
            '' => 'All orders',
            PaymentStatus::Paid->value => 'Paid',
            PaymentStatus::Pending->value => 'Pending',
            OrderStatus::Cancelled->value => 'Cancelled',
        ];

        $sourceOptions = [
            '' => 'All sources',
            OrderSource::Online->value => 'Online',
            OrderSource::Pos->value => 'POS',
        ];

        $fulfillmentOptions = [
            '' => 'All fulfillment',
            FulfillmentType::DineIn->value => 'Dine in',
            FulfillmentType::Takeaway->value => 'Takeaway',
            FulfillmentType::Delivery->value => 'Delivery',
        ];
    @endphp

    <div class="mb-6 flex flex-col gap-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-2">
                @foreach (request()->except(['payment_status', 'page']) as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                @foreach ($filterOptions as $value => $label)
                    <button
                        type="submit"
                        name="payment_status"
                        value="{{ $value }}"
                        class="admin-period-pill {{ ($paymentFilter ?? '') === $value ? 'admin-period-pill-active' : '' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </form>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <p class="text-sm text-brand-muted">
                    @if (($paymentFilter ?? '') === PaymentStatus::Paid->value)
                        Showing paid orders only
                    @elseif (($paymentFilter ?? '') === PaymentStatus::Pending->value)
                        Showing pending payment orders only
                    @elseif (($paymentFilter ?? '') === OrderStatus::Cancelled->value)
                        Showing cancelled orders only
                    @else
                        Showing all orders
                    @endif
                </p>

                <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-2 text-sm">
                    @foreach (request()->except(['per_page', 'page']) as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $item)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach

                    <label for="orders-per-page" class="whitespace-nowrap text-brand-muted">Show</label>
                    <select
                        id="orders-per-page"
                        name="per_page"
                        class="input-field mt-0 w-24 py-1.5 text-sm"
                        onchange="this.form.submit()"
                    >
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-2">
                @foreach (request()->except(['order_source', 'page']) as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                @foreach ($sourceOptions as $value => $label)
                    <button
                        type="submit"
                        name="order_source"
                        value="{{ $value }}"
                        class="admin-period-pill {{ ($orderSourceFilter ?? '') === $value ? 'admin-period-pill-active' : '' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </form>

            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-2">
                @foreach (request()->except(['fulfillment_type', 'page']) as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                @foreach ($fulfillmentOptions as $value => $label)
                    <button
                        type="submit"
                        name="fulfillment_type"
                        value="{{ $value }}"
                        class="admin-period-pill {{ ($fulfillmentTypeFilter ?? '') === $value ? 'admin-period-pill-active' : '' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </form>
        </div>
    </div>

    <x-admin-table-panel :page-ids="$orders->pluck('id')">
        <x-slot:bulkActions>
            <form
                method="POST"
                action="{{ route('admin.orders.invoices.export') }}"
                class="inline"
                @submit.prevent="if (!canExportInvoices) return; appendSelectedToForm($el, 'order_ids[]'); $el.submit();"
            >
                @csrf
                <button
                    type="submit"
                    class="btn-outline px-3 py-1.5 text-xs sm:text-sm"
                    :disabled="!canExportInvoices"
                    :class="{ 'opacity-50 cursor-not-allowed': !canExportInvoices }"
                >
                    Download PDFs
                </button>
            </form>

            <form
                method="POST"
                action="{{ route('admin.orders.invoices.print') }}"
                target="_blank"
                class="inline"
                @submit.prevent="if (!canExportInvoices) return; appendSelectedToForm($el, 'order_ids[]'); $el.submit();"
            >
                @csrf
                <button
                    type="submit"
                    class="btn-primary px-3 py-1.5 text-xs sm:text-sm"
                    :disabled="!canExportInvoices"
                    :class="{ 'opacity-50 cursor-not-allowed': !canExportInvoices }"
                >
                    Print invoices
                </button>
            </form>

            <x-admin-bulk-delete
                :action="route('admin.orders.bulk-destroy')"
                confirm="Delete the selected orders? This cannot be undone."
                label="Delete selected"
            />
        </x-slot:bulkActions>

        <table class="admin-data-table">
            <thead>
                <tr>
                    <x-admin-table-leading-header />
                    <x-admin-sort-th column="order_number" label="Order" />
                    <x-admin-sort-th column="customer" label="Customer" class="admin-cell-primary" />
                    <x-admin-sort-th column="total" label="Total" />
                    <x-admin-sort-th column="payment_status" label="Payment" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-md font-medium">Method</th>
                    <x-admin-sort-th column="status" label="Status" />
                    <x-admin-sort-th column="created_at" label="Date & time" class="admin-col-md" />
                    <th class="admin-table-cell admin-col-actions text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <x-admin-table-leading-cells :id="$order->id" :number="$orders->firstItem() + $loop->index" />
                        <td class="admin-table-cell whitespace-nowrap font-medium">{{ $order->order_number }}</td>
                        <td class="admin-table-cell admin-cell-primary">{{ $order->user?->name ?? $order->billing_full_name }}</td>
                        <td class="admin-table-cell whitespace-nowrap">GHS {{ number_format($order->total, 2) }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">
                            <x-admin-status-badge :status="$order->payment_status" />
                        </td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">
                            {{ $order->receiptPaymentMethodLabel() }}
                        </td>
                        <td class="admin-table-cell whitespace-nowrap">{{ $order->status->label() }}</td>
                        <td class="admin-table-cell admin-col-md whitespace-nowrap">
                            <div>{{ $order->created_at->format('M j, Y') }}</div>
                            <div class="text-xs text-brand-muted">{{ $order->created_at->format('g:i A') }}</div>
                        </td>
                        <td class="admin-table-cell admin-col-actions">
                            <x-admin-table-actions
                                :view-detail-url="route('admin.details.orders', $order)"
                                :edit-url="route('admin.orders.show', $order).'#delivery-tracking'"
                                edit-title="Update tracking"
                                :print-receipt-url="route('admin.orders.receipt', $order)"
                                :delete-url="route('admin.orders.destroy', $order)"
                                delete-confirm="Delete order {{ $order->order_number }}? This cannot be undone."
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="admin-table-cell py-8 text-center text-brand-muted">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin-table-panel>

    <x-admin-pagination :paginator="$orders" />
@endsection
