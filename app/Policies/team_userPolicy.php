<?php

namespace App\Policies;

use App\Models\team_user;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class team_userPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, team_user $team_user): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, team_user $team_user): bool
    {
    }

    public function delete(User $user, team_user $team_user): bool
    {
    }

    public function restore(User $user, team_user $team_user): bool
    {
    }

    public function forceDelete(User $user, team_user $team_user): bool
    {
    }
}
