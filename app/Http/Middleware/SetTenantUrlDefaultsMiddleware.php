<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetTenantUrlDefaultsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $sub = $request->route('tenantSubdomain')
            ?? session('tenantSubdomain')
            ?? (auth()->check() && method_exists(auth()->user(), 'currentTenant')
            ? optional(auth()->user()->currentTenant())->subdomain
            : null)
            ?? (auth()->check() ? optional(auth()->user()->tenant)->subdomain : null);

        if ($sub) {
            URL::defaults(['tenantSubdomain' => $sub]);
        }

        return $next($request);
    }
}
