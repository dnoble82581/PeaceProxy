<?php

namespace App\Policies;

use App\Models\Hook;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HookPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Hook $hook): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Hook $hook): bool
    {
    }

    public function delete(User $user, Hook $hook): bool
    {
    }

    public function restore(User $user, Hook $hook): bool
    {
    }

    public function forceDelete(User $user, Hook $hook): bool
    {
    }
}
