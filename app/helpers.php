<?php

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

function truncateString($string, $maxLength)
{
    $tuncatedString = strlen($string) > $maxLength
        ? substr($string, 0, $maxLength).'...'
        : $string;

    return $tuncatedString;
}
