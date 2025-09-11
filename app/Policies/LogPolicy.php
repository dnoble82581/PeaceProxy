<?php

namespace App\Policies;

use App\Models\Log;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Log $log): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Log $log): bool
    {
    }

    public function delete(User $user, Log $log): bool
    {
    }

    public function restore(User $user, Log $log): bool
    {
    }

    public function forceDelete(User $user, Log $log): bool
    {
    }
}
