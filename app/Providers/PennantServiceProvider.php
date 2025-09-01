<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Support\Plans;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

class PennantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Tell Pennant what “scope” to use when omitted: the current tenant id
        Feature::resolveScopeUsing(function () {
            // however you resolve your current tenant
            $tenant = app('currentTenant') ?? auth()->user()?->tenant;

            return $tenant?->id;
        });

        // Define features with default resolvers that fall back to your plan matrix.
        // Each resolver receives the *scope id* (tenant_id) because of resolveScopeUsing.
        Feature::define('reports.export.pdf', function ($tenantId) {
            $tenant = Tenant::find($tenantId);

            return $tenant && Plans::hasFeature($tenant, 'reports.export.pdf');
        });

        Feature::define('chat.reverb', function ($tenantId) {
            $tenant = Tenant::find($tenantId);

            return $tenant && Plans::hasFeature($tenant, 'chat.reverb');
        });

        Feature::define('support.priority', function ($tenantId) {
            $tenant = Tenant::find($tenantId);

            return $tenant && Plans::hasFeature($tenant, 'support.priority');
        });
    }
}
