<?php

namespace App\Models;

use App\Enums\FulfillmentType;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'dining_table_id',
        'subtotal',
        'delivery_fee',
        'tax',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'order_source',
        'fulfillment_type',
        'created_by',
        'billing_full_name',
        'billing_phone',
        'billing_email',
        'billing_address',
        'billing_city',
        'billing_country',
        'shipping_full_name',
        'shipping_phone',
        'shipping_email',
        'shipping_address',
        'shipping_city',
        'shipping_country',
        'shipping_region_id',
        'shipping_option_id',
        'shipping_region_name',
        'shipping_option_name',
        'customer_comment',
        'coupon_id',
        'coupon_code',
        'discount_amount',
        'shipping_fee',
        'paid_at',
        'kitchen_alert_sent_at',
        'payment_due_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => OrderStatus::class,
            'order_source' => OrderSource::class,
            'fulfillment_type' => FulfillmentType::class,
            'payment_status' => PaymentStatus::class,
            'paid_at' => 'datetime',
            'kitchen_alert_sent_at' => 'datetime',
            'payment_due_at' => 'datetime',
            'user_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diningTable(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPosOrder(): bool
    {
        return $this->order_source === OrderSource::Pos;
    }

    public function scopeKitchenActive(Builder $query): Builder
    {
        return $query
            ->where('payment_status', PaymentStatus::Paid)
            ->whereIn('status', [
                OrderStatus::Paid,
                OrderStatus::Processing,
                OrderStatus::ReadyForDelivery,
            ]);
    }

    public function kitchenColumn(): string
    {
        return match ($this->status) {
            OrderStatus::Paid => 'new',
            OrderStatus::Processing => 'preparing',
            OrderStatus::ReadyForDelivery => 'ready',
            default => 'new',
        };
    }

    public function kitchenStatusLabel(): string
    {
        return match ($this->status) {
            OrderStatus::Paid => 'New order',
            OrderStatus::Processing => 'Preparing',
            OrderStatus::ReadyForDelivery => 'Ready',
            default => $this->status->label(),
        };
    }

    public function kitchenFulfillmentLabel(): string
    {
        if ($this->fulfillment_type instanceof FulfillmentType) {
            return $this->fulfillment_type->label();
        }

        return $this->isPosOrder() ? 'In-store' : 'Online delivery';
    }

    public function kitchenSourceLabel(): string
    {
        if ($this->order_source instanceof OrderSource) {
            return $this->order_source->label();
        }

        return 'Online';
    }

    /**
     * @return list<OrderStatus>
     */
    public function kitchenAllowedStatuses(): array
    {
        return [
            OrderStatus::Processing,
            OrderStatus::ReadyForDelivery,
            OrderStatus::Delivered,
        ];
    }

    /**
     * @return array{status: OrderStatus, label: string}|null
     */
    public function kitchenNextAction(): ?array
    {
        return match ($this->status) {
            OrderStatus::Paid => [
                'status' => OrderStatus::Processing,
                'label' => 'Start preparing',
            ],
            OrderStatus::Processing => [
                'status' => OrderStatus::ReadyForDelivery,
                'label' => 'Mark ready',
            ],
            OrderStatus::ReadyForDelivery => $this->canCompleteFromKitchen() ? [
                'status' => OrderStatus::Delivered,
                'label' => 'Mark served',
            ] : null,
            default => null,
        };
    }

    public function canCompleteFromKitchen(): bool
    {
        if ($this->fulfillment_type === FulfillmentType::DineIn) {
            return true;
        }

        if ($this->fulfillment_type === FulfillmentType::Takeaway) {
            return true;
        }

        return $this->isPosOrder() && $this->fulfillment_type !== FulfillmentType::Delivery;
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function deliveryAssignment(): HasOne
    {
        return $this->hasOne(DeliveryAssignment::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * @return array<int, array{label: string, completed: bool, date: ?Carbon, current: bool}>
     */
    public function trackingSteps(): array
    {
        if (in_array($this->status, [OrderStatus::Cancelled, OrderStatus::Refunded], true)) {
            return [
                [
                    'label' => 'Order Created',
                    'completed' => true,
                    'date' => $this->created_at,
                    'current' => false,
                ],
                [
                    'label' => $this->status->label(),
                    'completed' => true,
                    'date' => $this->updated_at,
                    'current' => true,
                ],
            ];
        }

        $status = $this->status;

        $steps = [
            [
                'label' => 'Order Created',
                'completed' => true,
                'date' => $this->created_at,
                'current' => $status === OrderStatus::PendingPayment,
            ],
            [
                'label' => 'Payment Received',
                'completed' => $this->payment_status === PaymentStatus::Paid,
                'date' => $this->paid_at,
                'current' => $status === OrderStatus::Paid,
            ],
            [
                'label' => 'Processing',
                'completed' => in_array($status, [OrderStatus::Processing, OrderStatus::ReadyForDelivery, OrderStatus::Shipped, OrderStatus::Delivered], true),
                'date' => null,
                'current' => $status === OrderStatus::Processing,
            ],
            [
                'label' => 'Ready for Delivery',
                'completed' => in_array($status, [OrderStatus::ReadyForDelivery, OrderStatus::Shipped, OrderStatus::Delivered], true),
                'date' => null,
                'current' => $status === OrderStatus::ReadyForDelivery,
            ],
            [
                'label' => 'Shipped',
                'completed' => in_array($status, [OrderStatus::Shipped, OrderStatus::Delivered], true),
                'date' => null,
                'current' => $status === OrderStatus::Shipped,
            ],
            [
                'label' => 'Delivered',
                'completed' => $status === OrderStatus::Delivered,
                'date' => $status === OrderStatus::Delivered ? $this->updated_at : null,
                'current' => $status === OrderStatus::Delivered,
            ],
        ];

        if (! collect($steps)->contains(fn (array $step) => $step['current'])) {
            $firstIncomplete = collect($steps)->first(fn (array $step) => ! $step['completed']);
            if ($firstIncomplete) {
                $steps = collect($steps)->map(function (array $step) use ($firstIncomplete) {
                    $step['current'] = $step['label'] === $firstIncomplete['label'];

                    return $step;
                })->all();
            }
        }

        return $steps;
    }

    public function invoiceNumber(): string
    {
        return $this->order_number;
    }

    public function invoiceDate(): Carbon
    {
        return $this->paid_at ?? $this->created_at;
    }

    public function paymentMethodLabel(): string
    {
        return (string) config('shop.payment_method_label');
    }

    public function receiptPaymentMethodLabel(): string
    {
        if (filled($this->payment_method)) {
            return match ($this->payment_method) {
                'cash' => 'Cash',
                'card' => 'Card',
                'momo' => 'Mobile Money',
                'paystack' => 'Paystack',
                default => ucfirst(str_replace('_', ' ', $this->payment_method)),
            };
        }

        if ($this->payment?->paystackChannel()) {
            return ucfirst(str_replace('_', ' ', $this->payment->paystackChannel()));
        }

        return $this->paymentMethodLabel();
    }

    public function invoicePaymentMethodLabel(): string
    {
        return str_replace(
            [' Or ', 'Debit/Credit Cards'],
            [' / ', 'Debit or Credit Card'],
            $this->paymentMethodLabel(),
        );
    }

    public function invoiceShippingLabel(): string
    {
        if ((float) $this->shipping_fee > 0) {
            $label = config('shop.currency_symbol').number_format((float) $this->shipping_fee, 2);

            if ($this->shipping_option_name) {
                $label .= ' ('.$this->shipping_option_name.')';
            }

            return $label;
        }

        return (string) config('shop.invoice_accra_shipping_note');
    }

    public function deliveryFeeLabel(): string
    {
        $fee = (float) ($this->shipping_fee ?: $this->delivery_fee);

        if ($fee > 0) {
            $label = config('shop.currency_symbol').number_format($fee, 2);

            if ($this->shipping_option_name) {
                $label .= ' ('.$this->shipping_option_name.')';
            } elseif ($this->shipping_region_name) {
                $label .= ' ('.$this->shipping_region_name.')';
            }

            return $label;
        }

        if ($this->shipping_region_name) {
            return $this->shipping_region_name.' — pay rider on delivery';
        }

        return 'Pay rider on delivery';
    }

    public function formattedAddress(string $prefix = 'billing'): string
    {
        $lines = array_filter([
            $this->{"{$prefix}_full_name"},
            $this->{"{$prefix}_address"},
            $this->{"{$prefix}_city"},
            $this->{"{$prefix}_country"},
        ]);

        return implode(', ', $lines);
    }

    public function customerEmail(): ?string
    {
        $email = trim((string) ($this->shipping_email ?: $this->billing_email));

        if ($email !== '') {
            return $email;
        }

        return $this->user?->email;
    }

    /**
     * @return array<int, OrderStatus>
     */
    public function adminStatusOptions(): array
    {
        if ($this->payment_status === PaymentStatus::Paid) {
            return [
                OrderStatus::Paid,
                OrderStatus::Processing,
                OrderStatus::ReadyForDelivery,
                OrderStatus::Shipped,
                OrderStatus::Delivered,
                OrderStatus::Refunded,
            ];
        }

        return [
            OrderStatus::PendingPayment,
            OrderStatus::Paid,
            OrderStatus::Cancelled,
            OrderStatus::Refunded,
        ];
    }

    public function isTrackable(): bool
    {
        return $this->payment_status === PaymentStatus::Paid
            && ! in_array($this->status, [OrderStatus::Cancelled, OrderStatus::Refunded], true);
    }
}
