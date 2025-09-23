<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticatedMiddleware
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Build a tenant-aware dashboard URL
                return redirect()->to($this->tenantDashboardUrl($request));
            }
        }

        return $next($request);
    }

    protected function tenantDashboardUrl(Request $request): string
    {
        // Prefer route param if you're already on a tenant route
        $sub = $request->route('tenantSubdomain')
            // else: your app's "current tenant" accessor
            ?? (method_exists($request->user(), 'currentTenant')
            ? optional($request->user()->currentTenant())->subdomain
            : null)
            // else: single-tenant relation
            ?? optional($request->user()->tenant)->subdomain
            // else: first tenant the user belongs to
            ?? optional($request->user()->tenants()->first())->subdomain;

        if ($sub) {
            return route('dashboard', ['tenantSubdomain' => $sub]);
        }

        // Fallback if the user somehow has no tenant
        return route('central.landing'); // make sure this exists
    }
}
