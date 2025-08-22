<?php

namespace App\Policies;

use App\Models\Call;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CallPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Call $call): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Call $call): bool
    {
    }

    public function delete(User $user, Call $call): bool
    {
    }

    public function restore(User $user, Call $call): bool
    {
    }

    public function forceDelete(User $user, Call $call): bool
    {
    }
}
