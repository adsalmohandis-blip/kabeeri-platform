<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResetCustomerSessionForAdminLogin
{
    public const CUSTOMER_AUTH_SURFACE = 'kabeeri_auth_surface';

    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->is('admin/login')
            && Auth::check()
            && $request->session()->get(self::CUSTOMER_AUTH_SURFACE) === 'customer'
        ) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('filament.admin.auth.login');
        }

        return $next($request);
    }
}
