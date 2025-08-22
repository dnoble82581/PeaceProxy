<?php

namespace App\Policies;

use App\Models\Negotiation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NegotiationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Negotiation $negotiation): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Negotiation $negotiation): bool
    {
    }

    public function delete(User $user, Negotiation $negotiation): bool
    {
    }

    public function restore(User $user, Negotiation $negotiation): bool
    {
    }

    public function forceDelete(User $user, Negotiation $negotiation): bool
    {
    }
}
