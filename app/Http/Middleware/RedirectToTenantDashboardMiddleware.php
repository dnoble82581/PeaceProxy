<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectToTenantDashboardMiddleware
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Allow system and tooling routes to pass through untouched
        if ($request->is('horizon*') || $request->is('_boost/*')) {
            return $next($request);
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                // Redirect only if user visits the root route
                if ($user->tenant && $user->tenant->subdomain && $request->path() === '/') {
                    $subdomain = $user->tenant->subdomain;
                    $domain = config('app.domain');
                    $protocol = $request->secure() ? 'https://' : 'http://';

                    return redirect()->to("{$protocol}{$subdomain}.{$domain}/dashboard");
                }

                // If no tenant is found, log out or redirect
                if (! $user->tenant || ! $user->tenant->subdomain) {
                    Auth::guard($guard)->logout();

                    return redirect()->route('login')->withErrors(['message' => 'Tenant not found. Please log in again.']);
                }
            }
        }

        return $next($request);
    }
}
