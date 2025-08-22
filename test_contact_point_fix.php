<?php

use App\Models\ContactPoint;
use App\Models\Subject;
use App\Services\ContactPoint\ContactPointFetchingService;

// This script tests the updated ContactPointFetchingService

// Get the first subject
$subject = Subject::first();

if (!$subject) {
    echo "No subjects found in the database.\n";
    exit;
}

echo "Testing with Subject ID: " . $subject->id . "\n";

// Test the ContactPointFetchingService
$service = new ContactPointFetchingService();

try {
    // Test getContactPointsBySubject method
    echo "Testing getContactPointsBySubject method...\n";
    $contactPoints = $service->getContactPointsBySubject($subject);
    echo "Found " . $contactPoints->count() . " contact points using getContactPointsBySubject.\n";

    // Test getContactPointsBySubjectId method
    echo "Testing getContactPointsBySubjectId method...\n";
    $contactPointsById = $service->getContactPointsBySubjectId($subject->id);
    echo "Found " . $contactPointsById->count() . " contact points using getContactPointsBySubjectId.\n";

    // Test direct query using polymorphic relationship
    echo "Testing direct query using polymorphic relationship...\n";
    $directQuery = ContactPoint::where('contactable_id', $subject->id)
        ->where('contactable_type', Subject::class)
        ->get();
    echo "Found " . $directQuery->count() . " contact points using direct query.\n";

    echo "All tests completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
