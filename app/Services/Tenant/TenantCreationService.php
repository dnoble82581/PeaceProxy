<?php

namespace App\Services\Tenant;

use App\Models\Tenant;

class TenantCreationService
{
    public function createTenant(array $data): Tenant
    {
        return Tenant::create($data);

    }
}
