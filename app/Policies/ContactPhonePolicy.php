<?php

namespace App\Policies;

use App\Models\ContactPhone;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPhonePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, ContactPhone $contactPhone): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, ContactPhone $contactPhone): bool
    {
    }

    public function delete(User $user, ContactPhone $contactPhone): bool
    {
    }

    public function restore(User $user, ContactPhone $contactPhone): bool
    {
    }

    public function forceDelete(User $user, ContactPhone $contactPhone): bool
    {
    }
}
