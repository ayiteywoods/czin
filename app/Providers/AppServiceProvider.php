<?php

namespace App\Providers;

use App\Enums\AdminPermission;
use App\Models\CartItem;
use App\Models\DeliveryAssignment;
use App\Models\DiningTable;
use App\Models\LoyaltyAccount;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Services\CartService;
use App\Services\StoreSettingService;
use App\View\Composers\StorefrontComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Sanctum::usePersonalAccessTokenModel(\App\Models\PersonalAccessToken::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        app(StoreSettingService::class)->applyToConfig();

        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');

        View::composer('layouts.storefront', StorefrontComposer::class);

        Blade::if('adminCan', function (string $permission): bool {
            $user = auth()->user();

            if (! $user) {
                return false;
            }

            $enum = AdminPermission::tryFrom($permission);

            return $enum ? $user->hasAdminPermission($enum) : false;
        });

        Order::observe(OrderObserver::class);

        Route::bind('cartItem', function (string $value): CartItem {
            $cart = app(CartService::class)->resolve();

            return CartItem::query()
                ->where('cart_id', $cart->id)
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('table', fn (string $value) => DiningTable::query()->findOrFail($value));
        Route::bind('loyalty', fn (string $value) => LoyaltyAccount::query()->findOrFail($value));
        Route::bind('delivery', fn (string $value) => DeliveryAssignment::query()->findOrFail($value));
    }
}
