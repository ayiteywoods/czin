<?php

namespace App\Services;

use App\Enums\FulfillmentType;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TableStatus;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Support\OrderNumberGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosOrderService
{
    public function __construct(
        protected StockReservationService $stock,
        protected OrderPaymentService $payments,
    ) {}

    /**
     * @param  array{
     *     items: list<array{product_variant_id: int, quantity: int}>,
     *     fulfillment_type: string,
     *     dining_table_id?: int|null,
     *     customer_name?: string|null,
     *     customer_phone?: string|null,
     *     customer_comment?: string|null,
     *     payment_method: string,
     * }  $data
     */
    public function createOrder(array $data, int $adminId): Order
    {
        $items = $data['items'];

        if ($items === []) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one item to the order.',
            ]);
        }

        $fulfillmentType = FulfillmentType::from($data['fulfillment_type']);
        $table = null;

        if ($fulfillmentType === FulfillmentType::DineIn) {
            if (empty($data['dining_table_id'])) {
                throw ValidationException::withMessages([
                    'dining_table_id' => 'Select a table for dine-in orders.',
                ]);
            }

            $table = DiningTable::query()->findOrFail($data['dining_table_id']);
        }

        $resolvedItems = $this->resolveItems($items);
        $subtotal = (float) collect($resolvedItems)->sum(fn (array $item) => $item['unit_price'] * $item['quantity']);
        $tax = \App\Support\ShopTax::amountFor($subtotal);
        $total = round($subtotal + $tax, 2);
        $billing = $this->billingDefaults($data);

        return DB::transaction(function () use ($data, $adminId, $fulfillmentType, $table, $resolvedItems, $subtotal, $tax, $total, $billing) {
            $order = Order::query()->create([
                'order_number' => OrderNumberGenerator::next(),
                'user_id' => null,
                'dining_table_id' => $table?->id,
                'subtotal' => $subtotal,
                'delivery_fee' => 0,
                'shipping_fee' => 0,
                'tax' => $tax,
                'discount_amount' => 0,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'payment_status' => PaymentStatus::Pending,
                'status' => OrderStatus::PendingPayment,
                'order_source' => OrderSource::Pos,
                'fulfillment_type' => $fulfillmentType,
                'created_by' => $adminId,
                'billing_full_name' => $billing['billing_full_name'],
                'billing_phone' => $billing['billing_phone'],
                'billing_email' => $billing['billing_email'],
                'billing_address' => $billing['billing_address'],
                'billing_city' => $billing['billing_city'],
                'billing_country' => $billing['billing_country'],
                'customer_comment' => filled($data['customer_comment'] ?? null) ? $data['customer_comment'] : null,
            ]);

            foreach ($resolvedItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'],
                    'variant_sku' => $item['variant_sku'],
                    'variant_options' => $item['variant_options'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['unit_price'] * $item['quantity'],
                ]);
            }

            $payment = Payment::query()->create([
                'order_id' => $order->id,
                'user_id' => null,
                'reference' => $order->order_number.'_pos_'.time(),
                'provider' => 'manual',
                'amount' => $order->total,
                'currency' => config('shop.currency'),
                'channel' => $data['payment_method'],
                'status' => PaymentStatus::Pending,
                'metadata' => [
                    'source' => 'pos',
                    'created_by' => $adminId,
                ],
            ]);

            $this->payments->markAsPaid($order, $payment, [
                'id' => 'pos-'.$order->id.'-'.time(),
                'channel' => $data['payment_method'],
                'paid_at' => now()->toIso8601String(),
                'receipt_number' => 'POS-'.$order->order_number,
                'metadata' => [
                    'source' => 'pos',
                    'created_by' => $adminId,
                ],
            ]);

            $order->refresh();

            $order->update([
                'status' => OrderStatus::Processing,
            ]);

            if ($table) {
                $table->update(['status' => TableStatus::Occupied]);
            }

            return $order->load(['items', 'diningTable']);
        });
    }

    /**
     * @param  list<array{product_variant_id: int, quantity: int}>  $items
     * @return list<array{
     *     product_id: int,
     *     product_variant_id: int,
     *     product_name: string,
     *     product_sku: string|null,
     *     variant_sku: string|null,
     *     variant_options: array<string, mixed>|null,
     *     quantity: int,
     *     unit_price: float,
     * }>
     */
    protected function resolveItems(array $items): array
    {
        $resolved = [];

        foreach ($items as $index => $item) {
            $variant = ProductVariant::query()
                ->with('product')
                ->find($item['product_variant_id']);

            if (! $variant) {
                throw ValidationException::withMessages([
                    "items.{$index}.product_variant_id" => 'Selected menu option is no longer available.',
                ]);
            }

            $product = $variant->product;

            if (! $product instanceof Product || ! $product->isActive() || ! $variant->is_active) {
                throw ValidationException::withMessages([
                    "items.{$index}.product_variant_id" => 'One or more items are no longer available.',
                ]);
            }

            $quantity = max(1, (int) $item['quantity']);
            $available = $this->stock->sellableQuantity($variant);

            if ($available <= 0) {
                throw ValidationException::withMessages([
                    "items.{$index}.quantity" => "{$product->name} ({$variant->displayLabel()}) is out of stock.",
                ]);
            }

            if ($quantity > $available) {
                throw ValidationException::withMessages([
                    "items.{$index}.quantity" => "Only {$available} of {$product->name} ({$variant->displayLabel()}) available.",
                ]);
            }

            $resolved[] = [
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'variant_sku' => $variant->sku,
                'variant_options' => $variant->optionSnapshot(),
                'quantity' => $quantity,
                'unit_price' => $variant->sellingPrice(),
            ];
        }

        return $resolved;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     billing_full_name: string,
     *     billing_phone: string,
     *     billing_email: string,
     *     billing_address: string,
     *     billing_city: string,
     *     billing_country: string,
     * }
     */
    protected function billingDefaults(array $data): array
    {
        $store = StoreSetting::current();

        return [
            'billing_full_name' => filled($data['customer_name'] ?? null)
                ? (string) $data['customer_name']
                : 'Walk-in Customer',
            'billing_phone' => filled($data['customer_phone'] ?? null)
                ? (string) $data['customer_phone']
                : ($store->contact_phone ?: 'N/A'),
            'billing_email' => $store->contact_email ?: 'pos@czin.local',
            'billing_address' => $store->contact_address ?: 'In-store',
            'billing_city' => config('shop.default_city', 'Accra'),
            'billing_country' => config('shop.default_country'),
        ];
    }
}
