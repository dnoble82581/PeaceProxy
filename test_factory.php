<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Negotiation;

// Set up Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Clear previous test output
echo "Testing NegotiationFactory...\n\n";

try {
    // Create a negotiation using the factory
    $negotiation = Negotiation::factory()->create();

    // Output the negotiation details
    echo "Created negotiation with ID: " . $negotiation->id . "\n";
    echo "Title: " . $negotiation->title . "\n";
    echo "Summary: " . $negotiation->summary . "\n";
    echo "Status: " . $negotiation->status . "\n";
    echo "Type: " . $negotiation->type . "\n";
    echo "Location: " . $negotiation->location . "\n";

    // Test the active state
    $activeNegotiation = Negotiation::factory()->active()->create();
    echo "\nCreated active negotiation with ID: " . $activeNegotiation->id . "\n";
    echo "Status: " . $activeNegotiation->status . "\n";
    echo "Ended at: " . ($activeNegotiation->ended_at ? $activeNegotiation->ended_at : 'null') . "\n";

    // Test the resolved state
    $resolvedNegotiation = Negotiation::factory()->resolved()->create();
    echo "\nCreated resolved negotiation with ID: " . $resolvedNegotiation->id . "\n";
    echo "Status: " . $resolvedNegotiation->status . "\n";
    echo "Ended at: " . ($resolvedNegotiation->ended_at ? $resolvedNegotiation->ended_at : 'null') . "\n";

    echo "\nFactory test completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
