<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Assessment $assessment): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Assessment $assessment): bool
    {
    }

    public function delete(User $user, Assessment $assessment): bool
    {
    }

    public function restore(User $user, Assessment $assessment): bool
    {
    }

    public function forceDelete(User $user, Assessment $assessment): bool
    {
    }
}
