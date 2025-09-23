<?php

namespace App\Http\Middleware;

use App\Services\Tenant\TenantManagerService;
use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\CircularDependencyException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class IdentifyTenantMiddleware
{
    /**
     * @throws CircularDependencyException
     * @throws BindingResolutionException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('horizon*') || $request->routeIs('horizon.*')) {
            return $next($request);
        }

        $tenant = app(TenantManagerService::class)->resolve($request);

        if ($tenant === null) {
            // Proceed for central domain or routes that do not depend on a tenant
            return $next($request);
        }

        // Bind the tenant to the application
        App::instance('currentTenant', $tenant);

        // Ensure tenantSubdomain is automatically applied to route() URL generation
        // This prevents "Missing required parameter: tenantSubdomain" when calling route('dashboard') etc.
        URL::defaults(['tenantSubdomain' => $tenant->subdomain]);

        return $next($request);

    }
}
