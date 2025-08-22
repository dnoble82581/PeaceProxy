<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\ContactPoint\ContactPointCreationService;
use Illuminate\Support\Facades\DB;

// Create a test function to simulate the error condition
function testContactPointCreation()
{
    echo "Testing ContactPointCreationService with subject_id instead of contactable_id...\n";

    try {
        // Create a data array that includes subject_id but not contactable_id
        $data = [
            'subject_id' => 1, // Assuming a subject with ID 1 exists
            'tenant_id' => 1,  // Assuming a tenant with ID 1 exists
            'kind' => 'email',
            'label' => 'work',
            'is_primary' => true,
            'is_verified' => false,
            'priority' => 1,
            'email' => 'test@example.com',
        ];

        // Call the service
        $service = new ContactPointCreationService();

        // Wrap in a transaction and roll back to avoid actually creating records
        DB::beginTransaction();
        $contactPoint = $service->createContactPoint($data);

        // Check that the contact point was created with the correct values
        echo "Contact point created successfully!\n";
        echo "contactable_id: " . $contactPoint->contactable_id . "\n";
        echo "contactable_type: " . $contactPoint->contactable_type . "\n";

        // Roll back the transaction to avoid actually creating records
        DB::rollBack();

        echo "Test passed!\n";
    } catch (\Exception $e) {
        // If an exception is thrown, the test failed
        echo "Test failed: " . $e->getMessage() . "\n";

        // Roll back the transaction if it was started
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
    }
}

// Run the test
testContactPointCreation();
