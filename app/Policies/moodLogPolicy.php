<?php

namespace App\Policies;

use App\Models\moodLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class moodLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, moodLog $moodLog): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, moodLog $moodLog): bool
    {
    }

    public function delete(User $user, moodLog $moodLog): bool
    {
    }

    public function restore(User $user, moodLog $moodLog): bool
    {
    }

    public function forceDelete(User $user, moodLog $moodLog): bool
    {
    }
}
