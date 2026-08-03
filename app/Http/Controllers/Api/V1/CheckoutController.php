<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\FulfillmentType;
use App\Enums\OrderSource;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckoutRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ShippingRegion;
use App\Services\Api\ApiCartPresenter;
use App\Services\Api\ApiCartService;
use App\Services\Api\ReceiptPayloadService;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\CouponService;
use App\Services\PaystackService;
use App\Services\StockReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    use RespondsWithJson;

    public function summary(
        Request $request,
        ApiCartService $apiCart,
        ApiCartPresenter $presenter,
        CheckoutService $checkout,
        CouponService $coupons,
    ): JsonResponse {
        $cart = $apiCart->resolve()->load('items');
        $payload = $presenter->present($cart, $checkout);

        $payload['shipping_regions'] = ShippingRegion::query()
            ->with(['options' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('name')])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (ShippingRegion $region) => [
                'id' => $region->id,
                'name' => $region->name,
                'is_accra' => (bool) $region->is_accra,
                'options' => $region->options->map(fn ($option) => [
                    'id' => $option->id,
                    'name' => $option->name,
                    'price' => (float) $option->price,
                ])->values()->all(),
            ])->values()->all();

        if ($request->filled('coupon_code')) {
            $coupon = $coupons->findByCode($request->string('coupon_code')->toString());
            $payload['totals'] = $checkout->calculateTotals(
                $cart->items,
                (float) ($payload['totals']['delivery_fee'] ?? 0),
                $coupon,
            );
            $payload['coupon'] = $coupon ? ['code' => $coupon->code, 'type' => $coupon->type->value] : null;
        }

        return $this->success($payload);
    }

    public function store(
        CheckoutRequest $request,
        ApiCartService $apiCart,
        CheckoutService $checkout,
        StockReservationService $stock,
        PaystackService $paystack,
        ReceiptPayloadService $receipts,
    ): JsonResponse {
        $cart = $apiCart->resolve();
        $cartService = CartService::forCart($cart, $stock);

        // Temporarily bind cart for checkout service
        app()->instance(CartService::class, $cartService);

        $order = $checkout->placeOrder(
            $request->user(),
            $request->validated(),
            $request->boolean('save_address'),
        );

        $order->update([
            'order_source' => OrderSource::Online,
            'fulfillment_type' => FulfillmentType::Delivery,
        ]);

        $reference = $order->order_number.'_'.time();
        $paymentData = $paystack->initialize([
            'email' => $order->customerEmail() ?? $order->billing_email,
            'amount' => (int) round(((float) $order->total) * 100),
            'reference' => $reference,
            'callback_url' => $paystack->callbackUrl(),
            'currency' => config('shop.currency'),
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'source' => 'mobile_app',
            ],
        ]);

        Payment::query()->updateOrCreate(
            ['reference' => $paymentData['reference']],
            [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'provider' => 'paystack',
                'amount' => $order->total,
                'currency' => config('shop.currency'),
                'status' => PaymentStatus::Pending,
                'metadata' => [
                    'order_number' => $order->order_number,
                    'access_code' => $paymentData['access_code'],
                    'source' => 'mobile_app',
                ],
            ]
        );

        return $this->success([
            'order' => $receipts->forOrder($order),
            'payment' => [
                'provider' => 'paystack',
                'authorization_url' => $paymentData['authorization_url'],
                'access_code' => $paymentData['access_code'],
                'reference' => $paymentData['reference'],
                'public_key' => $paystack->publicKey(),
            ],
        ], status: 201);
    }
}
