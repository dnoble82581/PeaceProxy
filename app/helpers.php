<?php

use App\Models\Negotiation;
use App\Models\Tenant;
use App\Models\User;

function tenant(): ?Tenant
{
    $tenant = authUser()?->tenant;

    if (! $tenant) {
        // Log the user out if the tenant is not found
        Auth::logout();

        return null;
    }

    return $tenant;

}

function authUser(): ?User
{
    return auth()->user();
}

function authUserRole($negotiation)
{
    // Assuming $negotiation is the current negotiation instance, and you have the current user
    return $negotiation->users()
        ->where('user_id', authUser()->id)
        ->whereNull('negotiation_users.left_at') // Ensure the user hasn't "left" the negotiation
        ->first()?->pivot->role; // Retrieve the role from the pivot data

    // $role will now contain the role as a string.
}

/**
 * Check if a user is a participant in a negotiation.
 *
 * This function checks if the user exists in the negotiation_users pivot table
 * for the specified negotiation, regardless of their role or status.
 *
 * @param User $user The user to check
 * @param Negotiation $negotiation The negotiation to check
 * @return bool True if the user is a participant, false otherwise
 */
function isUserInNegotiation(User $user, Negotiation $negotiation): bool
{
    return $negotiation->users()
       ->where('user_id', $user->id)
       ->whereNull('negotiation_users.left_at') // Only consider active participations
       ->exists();
}
