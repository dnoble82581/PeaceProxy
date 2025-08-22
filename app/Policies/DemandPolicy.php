<?php

namespace App\Policies;

use App\Models\Demand;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DemandPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Demand $demand): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Demand $demand): bool
    {
    }

    public function delete(User $user, Demand $demand): bool
    {
    }

    public function restore(User $user, Demand $demand): bool
    {
    }

    public function forceDelete(User $user, Demand $demand): bool
    {
    }
}
