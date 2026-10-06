<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminPermission;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminHomeController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->hasAdminPermission(AdminPermission::Dashboard)) {
            return app()->call(DashboardController::class);
        }

        return redirect()->to($user?->defaultAdminRoute() ?? route('home'));
    }
}
