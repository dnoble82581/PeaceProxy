<?php

namespace App\Policies;

use App\Models\ContactPoint;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPointPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, ContactPoint $contactPoint): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, ContactPoint $contactPoint): bool
    {
    }

    public function delete(User $user, ContactPoint $contactPoint): bool
    {
    }

    public function restore(User $user, ContactPoint $contactPoint): bool
    {
    }

    public function forceDelete(User $user, ContactPoint $contactPoint): bool
    {
    }
}
