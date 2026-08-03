@props(['order'])

<article class="admin-kitchen-card">
    <div class="admin-kitchen-card-header">
        <div>
            <p class="admin-kitchen-card-number">#{{ $order['order_number'] }}</p>
            <p class="admin-kitchen-card-time">{{ $order['created_at'] }}</p>
        </div>
        <span class="admin-kitchen-pill admin-kitchen-pill-{{ $order['status'] }}">
            {{ $order['status_label'] }}
        </span>
    </div>

    <div class="admin-kitchen-card-meta">
        <span>{{ $order['fulfillment_label'] }}</span>
        <span>{{ $order['source_label'] }}</span>
        @if ($order['table'])
            <span class="font-semibold text-brand-black">
                {{ $order['table']['code'] }} · {{ $order['table']['name'] }}
            </span>
        @endif
    </div>

    @if ($order['customer_name'] && $order['customer_name'] !== 'Walk-in Customer')
        <p class="admin-kitchen-card-customer">{{ $order['customer_name'] }}</p>
    @endif

    <ul class="admin-kitchen-items">
        @foreach ($order['items'] as $item)
            <li class="admin-kitchen-item">
                <span class="admin-kitchen-item-qty">{{ $item['quantity'] }}×</span>
                <div class="min-w-0">
                    <p class="admin-kitchen-item-name">{{ $item['name'] }}</p>
                    @if (! empty($item['options']))
                        <p class="admin-kitchen-item-options">
                            @foreach ($item['options'] as $key => $value)
                                @if (filled($value))
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}@if (! $loop->last), @endif
                                @endif
                            @endforeach
                        </p>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>

    @if ($order['customer_comment'])
        <div class="admin-kitchen-note">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Kitchen note</p>
            <p class="mt-1 text-sm text-amber-900">{{ $order['customer_comment'] }}</p>
        </div>
    @endif

    <div class="admin-kitchen-card-actions">
        @if (! empty($order['print_url']))
            <a href="{{ $order['print_url'] }}" target="_blank" rel="noopener" class="admin-kitchen-print-link">
                Print ticket
            </a>
        @endif
    </div>

    @if ($order['next_action'])
        <form
            method="POST"
            action="{{ $order['update_url'] }}"
            class="admin-kitchen-action-form"
            data-kitchen-form
        >
            @csrf
            @method('PATCH')
            <button type="submit" class="admin-kitchen-action" data-kitchen-submit>
                {{ $order['next_action']['label'] }}
            </button>
        </form>
    @elseif ($order['status'] === 'ready_for_delivery')
        <p class="admin-kitchen-waiting">Waiting for pickup / service</p>
    @endif
</article>
