<?php

use App\Models\User;
use App\Models\Negotiation;

// Example usage of the getCurrentRoleInNegotiation method

// Get a user
$user = User::first();

// Get a negotiation
$negotiation = Negotiation::first();

if ($user && $negotiation) {
    // Get the user's current role in the negotiation
    $role = $user->getCurrentRoleInNegotiation($negotiation->id);

    if ($role) {
        echo "User's current role in negotiation '{$negotiation->title}': " . $role->label() . "\n";
        echo "Role description: " . $role->description() . "\n";
    } else {
        echo "User is not currently participating in negotiation '{$negotiation->title}'.\n";
    }
} else {
    echo "No users or negotiations found in the database.\n";
}

// Example with a negotiation the user is not part of
if ($user) {
    // Assuming 999999 is an ID that doesn't exist or the user is not part of
    $role = $user->getCurrentRoleInNegotiation(999999);

    if ($role) {
        echo "User's current role in non-existent negotiation: " . $role->label() . "\n";
    } else {
        echo "User is not currently participating in the non-existent negotiation (expected result).\n";
    }
}
