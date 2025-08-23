<?php

namespace App\Policies;

use App\Models\Pin;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PinPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Pin $pin): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Pin $pin): bool
    {
    }

    public function delete(User $user, Pin $pin): bool
    {
    }

    public function restore(User $user, Pin $pin): bool
    {
    }

    public function forceDelete(User $user, Pin $pin): bool
    {
    }
}
