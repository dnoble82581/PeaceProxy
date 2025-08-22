<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectToTenantDashboardMiddleware
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                // Redirect only if user visits the root route
                if ($user->tenant && $user->tenant->subdomain && $request->path() === '/') {
                    $subdomain = $user->tenant->subdomain;

                    return redirect()->to("http://{$subdomain}." . config('app.domain') . "/dashboard");
                }

                // If no tenant is found, log out or redirect
                if (! $user->tenant || ! $user->tenant->subdomain) {
                    logger('No tenant found for user: '.$user->id); // Optional logging
                    Auth::guard($guard)->logout();

                    return redirect()->route('login')->withErrors(['message' => 'Tenant not found. Please log in again.']);
                }
            }
        }

        return $next($request);
    }
}
