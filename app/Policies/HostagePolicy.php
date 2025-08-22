<?php

namespace App\Policies;

use App\Models\Hostage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Hostage $hostage): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Hostage $hostage): bool
    {
    }

    public function delete(User $user, Hostage $hostage): bool
    {
    }

    public function restore(User $user, Hostage $hostage): bool
    {
    }

    public function forceDelete(User $user, Hostage $hostage): bool
    {
    }
}
