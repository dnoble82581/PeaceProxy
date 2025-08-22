<?php

namespace App\Policies;

use App\Models\PhoneNumber;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhoneNumberPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, PhoneNumber $phoneNumber): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, PhoneNumber $phoneNumber): bool
    {
    }

    public function delete(User $user, PhoneNumber $phoneNumber): bool
    {
    }

    public function restore(User $user, PhoneNumber $phoneNumber): bool
    {
    }

    public function forceDelete(User $user, PhoneNumber $phoneNumber): bool
    {
    }
}
