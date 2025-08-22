<?php

namespace App\Policies;

use App\Models\ContactEmail;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactEmailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, ContactEmail $contactEmail): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, ContactEmail $contactEmail): bool
    {
    }

    public function delete(User $user, ContactEmail $contactEmail): bool
    {
    }

    public function restore(User $user, ContactEmail $contactEmail): bool
    {
    }

    public function forceDelete(User $user, ContactEmail $contactEmail): bool
    {
    }
}
