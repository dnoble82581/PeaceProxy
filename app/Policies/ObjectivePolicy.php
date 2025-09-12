<?php

namespace App\Policies;

use App\Models\Objective;
use App\Models\User;
use App\Services\Negotiation\NegotiationFetchingService;
use Illuminate\Auth\Access\HandlesAuthorization;

class ObjectivePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Objective $objective): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Objective $objective): bool
    {
        $negotiation = app(NegotiationFetchingService::class)->getNegotiationById($objective->negotiation_id);
        return authUserRole($negotiation)->value === 'team_leader';

    }

    public function delete(User $user, Objective $objective): bool
    {
    }

    public function restore(User $user, Objective $objective): bool
    {
    }

    public function forceDelete(User $user, Objective $objective): bool
    {
    }
}
