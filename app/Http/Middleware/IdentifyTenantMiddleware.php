<?php

namespace App\Http\Middleware;

use App\Services\Tenant\TenantManagerService;
use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\CircularDependencyException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class IdentifyTenantMiddleware
{
    /**
     * @throws CircularDependencyException
     * @throws BindingResolutionException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('horizon*')) {
            return $next($request);
        }

        $tenant = app(TenantManagerService::class)->resolve($request);

        if ($tenant === null) {
            // Log or handle missing tenants (optional)
            logger()->warning('No tenant identified for the request.', [
                'url' => $request->fullUrl(),
                'user' => auth()->user(),
            ]);

            // Proceed for central domain or routes that do not depend on a tenant
            return $next($request);
        }

        // Bind the tenant to the application
        App::instance('currentTenant', $tenant);

        return $next($request);

    }
}
