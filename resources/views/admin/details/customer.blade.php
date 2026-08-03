<dl class="grid gap-4 sm:grid-cols-2">
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-brand-muted">Name</dt>
        <dd class="mt-1 font-medium">{{ $user->name }}</dd>
    </div>
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-brand-muted">Email</dt>
        <dd class="mt-1">{{ $user->email }}</dd>
    </div>
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-brand-muted">Phone</dt>
        <dd class="mt-1">{{ $user->phone ?? '—' }}</dd>
    </div>
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-brand-muted">Status</dt>
        <dd class="mt-1">{{ $user->is_active ? 'Active' : 'Inactive' }}</dd>
    </div>
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-brand-muted">Total orders</dt>
        <dd class="mt-1">{{ $user->orders_count }}</dd>
    </div>
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-brand-muted">Joined</dt>
        <dd class="mt-1">{{ $user->created_at->format('M j, Y') }}</dd>
    </div>
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-brand-muted">Total spend</dt>
        <dd class="mt-1">{{ config('shop.currency_symbol') }} {{ number_format($totalSpend, 2) }}</dd>
    </div>
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-brand-muted">Average order</dt>
        <dd class="mt-1">{{ config('shop.currency_symbol') }} {{ number_format($averageOrder, 2) }}</dd>
    </div>
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-brand-muted">Last order</dt>
        <dd class="mt-1">{{ $lastOrderDate ? \Illuminate\Support\Carbon::parse($lastOrderDate)->format('M j, Y') : '—' }}</dd>
    </div>
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-brand-muted">Loyalty points</dt>
        <dd class="mt-1">{{ number_format($user->loyaltyAccount?->points_balance ?? 0) }}</dd>
    </div>
</dl>

@if ($user->customerTags->isNotEmpty())
    <div class="mt-6">
        <h3 class="text-sm font-semibold uppercase tracking-wide">Tags</h3>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach ($user->customerTags as $tag)
                <form method="POST" action="{{ route('admin.customers.destroy-tag', [$user, $tag]) }}" class="inline-flex items-center gap-1 bg-neutral-100 px-2.5 py-1 text-xs font-medium text-brand-black">
                    @csrf
                    @method('DELETE')
                    <span>{{ $tag->tag }}</span>
                    <button type="submit" class="text-brand-muted hover:text-brand-red" title="Remove tag">&times;</button>
                </form>
            @endforeach
        </div>
    </div>
@endif

<form method="POST" action="{{ route('admin.customers.store-tag', $user) }}" class="mt-4 flex gap-2">
    @csrf
    <input type="text" name="tag" class="input-field flex-1" placeholder="Add tag (e.g. VIP, Regular)" maxlength="50">
    <button type="submit" class="btn-outline shrink-0">Add tag</button>
</form>
@error('tag')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

<form method="POST" action="{{ route('admin.customers.update-notes', $user) }}" class="mt-6 space-y-3">
    @csrf
    @method('PATCH')
    <h3 class="text-sm font-semibold uppercase tracking-wide">Admin notes</h3>
    <textarea name="admin_notes" rows="4" class="input-field" placeholder="Internal notes about this customer…">{{ old('admin_notes', $user->admin_notes) }}</textarea>
    @error('admin_notes')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    <button type="submit" class="btn-primary">Save notes</button>
</form>

@if ($recentOrders->isNotEmpty())
    <div class="mt-6">
        <h3 class="text-sm font-semibold uppercase tracking-wide">Recent orders</h3>
        <table class="mt-3 w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-200 text-left text-xs uppercase tracking-wide text-brand-muted">
                    <th class="py-2">Order</th>
                    <th class="py-2">Date</th>
                    <th class="py-2">Status</th>
                    <th class="py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach ($recentOrders as $order)
                    <tr>
                        <td class="py-2 font-medium">{{ $order->order_number }}</td>
                        <td class="py-2">{{ $order->created_at->format('M j, Y') }}</td>
                        <td class="py-2">{{ $order->status->label() }}</td>
                        <td class="py-2 text-right">{{ config('shop.currency_symbol') }} {{ number_format($order->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
