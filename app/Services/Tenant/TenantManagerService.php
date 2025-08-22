<?php

namespace App\Services\Tenant;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TenantManagerService
{
    protected ?Tenant $tenant = null;

    public function __construct()
    {
    }

    public function resolve(Request $request): ?Tenant
    {
        $host = $request->getHost(); // e.g. acme.peaceproxypro_2.test
        $subdomain = explode('.', $host)[0];

        if ($this->isCentralDomain($host)) {
            return null;
        }

        // Optional: cache tenant lookup for perf
        return $this->tenant = Cache::remember("tenant_by_subdomain:$subdomain", 60, function () use ($subdomain) {
            return Tenant::where('subdomain', $subdomain)->first();
        });
    }

    public function isCentralDomain(string $host): bool
    {
        return in_array($host, ['peaceproxypro_2.test', 'www.peaceproxypro_2.test']);
    }

    public function get(): ?Tenant
    {
        return $this->tenant;
    }
}
