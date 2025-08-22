<?php

namespace App\Services\User;

use App\Models\Tenant;

class CreateUserService
{
    public function createUserFromTenant(Tenant $tenant, $data)
    {
        return $tenant->users()->create($data);
    }
}
