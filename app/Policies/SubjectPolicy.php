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
        return false;
    }

    public function view(User $user, Subject $subject): bool
    {
        // A user may view a subject only if they belong to the same tenant
        // and they share at least one negotiation with that subject.
        if ($user->tenant_id !== $subject->tenant_id) {
            return false;
        }

        return $user->negotiations()
            ->whereHas('subjects', function ($q) use ($subject) {
                $q->where('subjects.id', $subject->id);
            })
            ->exists();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Subject $subject): bool
    {
        return false;
    }

    public function delete(User $user, Subject $subject): bool
    {
        return false;
    }

    public function restore(User $user, Subject $subject): bool
    {
        return false;
    }

    public function forceDelete(User $user, Subject $subject): bool
    {
        return false;
    }
}
