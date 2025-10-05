<?php

namespace App\Services\Tenant;

use App\Enums\Team\TeamDiscipline;
use App\Models\Team;
use App\Models\Tenant;

class TenantCreationService
{
    public function createTenant(array $data): Tenant
    {
        $newTenant = Tenant::create($data);

        $this->createTeamsForTenant($newTenant->id);

        return $newTenant;
    }

    private function createTeamsForTenant(int $tenantId)
    {
        foreach (TeamDiscipline::cases() as $teamDiscipline) {
            Team::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'slug' => $teamDiscipline->value,
                    'name' => $teamDiscipline->label(),
                ]
            );
        }
    }
}
