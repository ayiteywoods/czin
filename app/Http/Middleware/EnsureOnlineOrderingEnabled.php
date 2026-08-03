<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnlineOrderingEnabled
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('shop.online_ordering_enabled', true)) {
            return $next($request);
        }

        if ($request->user()?->isAdmin()) {
            return $next($request);
        }

        $message = 'Online ordering is temporarily unavailable. Please visit us in person or call to place an order.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return redirect()
            ->route('home')
            ->with('error', $message);
    }
}
