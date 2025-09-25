<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Activity $activity): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Activity $activity): bool
    {
    }

    public function delete(User $user, Activity $activity): bool
    {
    }

    public function restore(User $user, Activity $activity): bool
    {
    }

    public function forceDelete(User $user, Activity $activity): bool
    {
    }
}
