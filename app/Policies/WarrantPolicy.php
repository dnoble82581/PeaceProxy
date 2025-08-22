<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warrant;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarrantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Warrant $warrant): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Warrant $warrant): bool
    {
    }

    public function delete(User $user, Warrant $warrant): bool
    {
    }

    public function restore(User $user, Warrant $warrant): bool
    {
    }

    public function forceDelete(User $user, Warrant $warrant): bool
    {
    }
}
