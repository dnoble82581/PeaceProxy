<?php

namespace App\Policies;

use App\Models\Trigger;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TriggerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Trigger $trigger): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Trigger $trigger): bool
    {
    }

    public function delete(User $user, Trigger $trigger): bool
    {
    }

    public function restore(User $user, Trigger $trigger): bool
    {
    }

    public function forceDelete(User $user, Trigger $trigger): bool
    {
    }
}
