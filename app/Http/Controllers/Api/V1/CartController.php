<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCartItemRequest;
use App\Http\Requests\Api\V1\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\Api\ApiCartPresenter;
use App\Services\Api\ApiCartService;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\ProductVariantResolver;
use App\Services\StockReservationService;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    use RespondsWithJson;

    public function show(ApiCartService $apiCart, ApiCartPresenter $presenter, CheckoutService $checkout): JsonResponse
    {
        return $this->success($presenter->present($apiCart->resolve(), $checkout));
    }

    public function store(
        StoreCartItemRequest $request,
        ApiCartService $apiCart,
        ApiCartPresenter $presenter,
        CheckoutService $checkout,
        ProductVariantResolver $variantResolver,
        StockReservationService $stock,
    ): JsonResponse {
        $product = Product::query()->findOrFail($request->integer('product_id'));
        abort_unless($product->isVisibleOnStorefront() && ! $product->is_86ed, 404);

        $variant = $variantResolver->resolveForProduct(
            $product,
            $request->string('variant_size')->toString(),
            $request->string('variant_color')->toString(),
            $request->filled('variant_heel') ? $request->string('variant_heel')->toString() : null,
        );

        $specialRequest = strcasecmp($request->string('variant_color')->toString(), 'Custom') === 0
            ? $request->string('special_request')->toString()
            : null;

        $cart = $apiCart->resolve();
        $cartService = CartService::forCart($cart, $stock);
        $cartService->add($product, $variant, $request->integer('quantity', 1), $specialRequest);

        return $this->success($presenter->present($cart->fresh(), $checkout), status: 201);
    }

    public function update(
        UpdateCartItemRequest $request,
        CartItem $cartItem,
        ApiCartService $apiCart,
        ApiCartPresenter $presenter,
        CheckoutService $checkout,
        StockReservationService $stock,
    ): JsonResponse {
        $cart = $apiCart->resolve();
        abort_unless((int) $cartItem->cart_id === (int) $cart->id, 404);

        $cartService = CartService::forCart($cart, $stock);
        $cartService->updateQuantity($cartItem, $request->integer('quantity'));

        return $this->success($presenter->present($cart->fresh(), $checkout));
    }

    public function destroy(
        CartItem $cartItem,
        ApiCartService $apiCart,
        ApiCartPresenter $presenter,
        CheckoutService $checkout,
        StockReservationService $stock,
    ): JsonResponse {
        $cart = $apiCart->resolve();
        abort_unless((int) $cartItem->cart_id === (int) $cart->id, 404);

        CartService::forCart($cart, $stock)->remove($cartItem);

        return $this->success($presenter->present($cart->fresh(), $checkout));
    }

    public function clear(
        ApiCartService $apiCart,
        ApiCartPresenter $presenter,
        CheckoutService $checkout,
        StockReservationService $stock,
    ): JsonResponse {
        $cart = $apiCart->resolve();
        CartService::forCart($cart, $stock)->clear();

        return $this->success($presenter->present($cart->fresh(), $checkout));
    }
}
