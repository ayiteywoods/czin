<?php

use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController as ApiCartController;
use App\Http\Controllers\Api\V1\CheckoutController as ApiCheckoutController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\KitchenController as ApiKitchenController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\OrderController as ApiOrderController;
use App\Http\Controllers\Api\V1\PosController as ApiPosController;
use App\Http\Controllers\Api\V1\ReceiptController as ApiReceiptController;
use App\Http\Controllers\Api\V1\TableController as ApiTableController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);

    Route::get('menu', [MenuController::class, 'index']);
    Route::get('menu/products/{product:slug}', [MenuController::class, 'show']);

    Route::middleware('api.cart')->group(function () {
        Route::get('cart', [ApiCartController::class, 'show']);
        Route::post('cart/items', [ApiCartController::class, 'store']);
        Route::patch('cart/items/{cartItem}', [ApiCartController::class, 'update']);
        Route::delete('cart/items/{cartItem}', [ApiCartController::class, 'destroy']);
        Route::delete('cart', [ApiCartController::class, 'clear']);

        Route::get('checkout/summary', [ApiCheckoutController::class, 'summary']);
        Route::post('checkout', [ApiCheckoutController::class, 'store']);
    });

    Route::middleware('api.auth')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('account/orders', [AccountController::class, 'orders']);
        Route::get('account/orders/{order}', [AccountController::class, 'showOrder']);
    });

    Route::middleware(['api.auth', 'api.admin'])->group(function () {
        Route::middleware('api.admin.permission:dashboard')->get('dashboard/summary', [DashboardController::class, 'summary']);

        Route::middleware('api.admin.permission:pos,orders')->group(function () {
            Route::get('pos/bootstrap', [ApiPosController::class, 'bootstrap']);
            Route::post('pos/orders', [ApiPosController::class, 'storeOrder']);
            Route::get('pos/orders/{order}', [ApiPosController::class, 'showOrder']);
        });

        Route::middleware('api.admin.permission:orders')->group(function () {
            Route::get('orders', [ApiOrderController::class, 'index']);
            Route::get('orders/{order}', [ApiOrderController::class, 'show']);
            Route::patch('orders/{order}/status', [ApiOrderController::class, 'updateStatus']);
            Route::get('orders/{order}/receipt', [ApiReceiptController::class, 'show']);
        });

        Route::middleware('api.admin.permission:kitchen')->group(function () {
            Route::get('kitchen/board', [ApiKitchenController::class, 'board']);
            Route::patch('kitchen/orders/{order}/status', [ApiKitchenController::class, 'advanceStatus']);
        });

        Route::middleware('api.admin.permission:tables')->group(function () {
            Route::get('tables', [ApiTableController::class, 'index']);
            Route::patch('tables/{table}/status', [ApiTableController::class, 'updateStatus']);
        });
    });
});
