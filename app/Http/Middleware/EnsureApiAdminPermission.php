<?php

namespace App\Http\Middleware;

use App\Enums\AdminPermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiAdminPermission
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin() || ! $user->is_active) {
            return response()->json([
                'message' => 'Admin access required.',
            ], Response::HTTP_FORBIDDEN);
        }

        foreach ($permissions as $permission) {
            $enum = AdminPermission::tryFrom($permission);

            if ($enum && $user->hasAdminPermission($enum)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'You do not have permission to access this resource.',
        ], Response::HTTP_FORBIDDEN);
    }
}
