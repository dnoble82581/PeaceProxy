<?php

namespace App\Services\Tenant;

use App\Models\Tenant;

class UpdateTenantService
{
    public function updateTenant(Tenant $tenant, array $data): Tenant
    {
        // Filter out null/empty string values to avoid overwriting with blanks
        $filtered = array_filter(
            $data,
            static fn ($v) => $v !== null && $v !== ''
        );

        $tenant->update($filtered);

        return $tenant->refresh();
    }
}
