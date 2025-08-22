<?php

namespace App\Policies;

use App\Models\Email;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Email $email): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Email $email): bool
    {
    }

    public function delete(User $user, Email $email): bool
    {
    }

    public function restore(User $user, Email $email): bool
    {
    }

    public function forceDelete(User $user, Email $email): bool
    {
    }
}
