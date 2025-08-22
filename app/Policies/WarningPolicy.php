<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warning;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarningPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Warning $warning): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Warning $warning): bool
    {
    }

    public function delete(User $user, Warning $warning): bool
    {
    }

    public function restore(User $user, Warning $warning): bool
    {
    }

    public function forceDelete(User $user, Warning $warning): bool
    {
    }
}
