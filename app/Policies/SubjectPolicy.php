<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Subject $subject): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Subject $subject): bool
    {
    }

    public function delete(User $user, Subject $subject): bool
    {
    }

    public function restore(User $user, Subject $subject): bool
    {
    }

    public function forceDelete(User $user, Subject $subject): bool
    {
    }
}
