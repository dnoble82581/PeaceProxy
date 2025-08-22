<?php

namespace App\Policies;

use App\Models\ContactAddress;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactAddressPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, ContactAddress $contactAddress): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, ContactAddress $contactAddress): bool
    {
    }

    public function delete(User $user, ContactAddress $contactAddress): bool
    {
    }

    public function restore(User $user, ContactAddress $contactAddress): bool
    {
    }

    public function forceDelete(User $user, ContactAddress $contactAddress): bool
    {
    }
}
