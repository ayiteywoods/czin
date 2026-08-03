<?php

use App\Http\Middleware\AuthenticateApiToken;
use App\Http\Middleware\EnsureApiAdmin;
use App\Http\Middleware\EnsureApiAdminPermission;
use App\Http\Middleware\EnsureAdminPermission;
use App\Http\Middleware\EnsureCartNotEmpty;
use App\Http\Middleware\ResolveApiCart;
use App\Http\Middleware\EnsureOnlineOrderingEnabled;
use App\Http\Middleware\EnsureStorefrontNotInMaintenance;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'admin.permission' => EnsureAdminPermission::class,
            'api.auth' => AuthenticateApiToken::class,
            'api.admin' => EnsureApiAdmin::class,
            'api.admin.permission' => EnsureApiAdminPermission::class,
            'api.cart' => ResolveApiCart::class,
            'cart.not_empty' => EnsureCartNotEmpty::class,
            'storefront.maintenance' => EnsureStorefrontNotInMaintenance::class,
            'storefront.online_ordering' => EnsureOnlineOrderingEnabled::class,
        ]);

        $middleware->api(prepend: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        ]);

        $middleware->validateCsrfTokens(except: [
            'paystack/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
