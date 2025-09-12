<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Negotiation;

// Example usage of the isUserInNegotiation function

// Get a user
$user = User::first();

// Get a negotiation
$negotiation = Negotiation::first();

if ($user && $negotiation) {
    // Check if the user is in the negotiation
    $isInNegotiation = isUserInNegotiation($user, $negotiation);

    if ($isInNegotiation) {
        echo "User '{$user->name}' is a participant in negotiation '{$negotiation->title}'.\n";
    } else {
        echo "User '{$user->name}' is NOT a participant in negotiation '{$negotiation->title}'.\n";
    }
} else {
    echo "No users or negotiations found in the database.\n";
}

// Example with a negotiation the user is likely not part of
if ($user) {
    // Get the last negotiation (assuming it's different from the first one)
    $lastNegotiation = Negotiation::orderBy('id', 'desc')->first();

    if ($lastNegotiation && $lastNegotiation->id !== $negotiation->id) {
        $isInLastNegotiation = isUserInNegotiation($user, $lastNegotiation);

        if ($isInLastNegotiation) {
            echo "User '{$user->name}' is a participant in negotiation '{$lastNegotiation->title}'.\n";
        } else {
            echo "User '{$user->name}' is NOT a participant in negotiation '{$lastNegotiation->title}'.\n";
        }
    }
}
