<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Set up Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Clear previous test output
echo "Testing DatabaseSeeder...\n\n";

try {
    // Run the database seeder
    echo "Running DatabaseSeeder...\n";
    $seeder = new Database\Seeders\DatabaseSeeder();
    $seeder->run();

    // Count the records
    $tenantCount = \App\Models\Tenant::count();
    $userCount = \App\Models\User::count();
    $negotiationCount = \App\Models\Negotiation::count();
    $subjectCount = \App\Models\Subject::count();
    $negotiationUserCount = DB::table('negotiation_users')->count();
    $negotiationSubjectCount = DB::table('negotiation_subjects')->count();

    // Output the counts
    echo "Created $tenantCount tenants\n";
    echo "Created $userCount users\n";
    echo "Created $negotiationCount negotiations\n";
    echo "Created $subjectCount subjects\n";
    echo "Created $negotiationUserCount negotiation_user records\n";
    echo "Created $negotiationSubjectCount negotiation_subject records\n\n";

    // Verify the counts match expectations
    $expectedTenantCount = 10;
    $expectedUserCount = 10 * 5; // 10 tenants * 5 users per tenant
    $expectedNegotiationCount = 10 * 2; // 10 tenants * 2 negotiations per tenant

    // Subjects, negotiation_users, and negotiation_subjects are variable, so we can only check ranges
    $minExpectedSubjectCount = $expectedNegotiationCount; // At least 1 subject per negotiation
    $maxExpectedSubjectCount = $expectedNegotiationCount * 3; // At most 3 subjects per negotiation

    $minExpectedNegotiationUserCount = $expectedNegotiationCount; // At least 1 user per negotiation
    $maxExpectedNegotiationUserCount = $expectedNegotiationCount * 3; // At most 3 users per negotiation

    $minExpectedNegotiationSubjectCount = $expectedNegotiationCount; // At least 1 subject per negotiation
    $maxExpectedNegotiationSubjectCount = $expectedNegotiationCount * 3; // At most 3 subjects per negotiation

    // Check if counts match expectations
    $success = true;

    if ($tenantCount != $expectedTenantCount) {
        echo "ERROR: Expected $expectedTenantCount tenants, but got $tenantCount\n";
        $success = false;
    }

    if ($userCount != $expectedUserCount) {
        echo "ERROR: Expected $expectedUserCount users, but got $userCount\n";
        $success = false;
    }

    if ($negotiationCount != $expectedNegotiationCount) {
        echo "ERROR: Expected $expectedNegotiationCount negotiations, but got $negotiationCount\n";
        $success = false;
    }

    if ($subjectCount < $minExpectedSubjectCount || $subjectCount > $maxExpectedSubjectCount) {
        echo "ERROR: Expected between $minExpectedSubjectCount and $maxExpectedSubjectCount subjects, but got $subjectCount\n";
        $success = false;
    }

    if ($negotiationUserCount < $minExpectedNegotiationUserCount || $negotiationUserCount > $maxExpectedNegotiationUserCount) {
        echo "ERROR: Expected between $minExpectedNegotiationUserCount and $maxExpectedNegotiationUserCount negotiation_user records, but got $negotiationUserCount\n";
        $success = false;
    }

    if ($negotiationSubjectCount < $minExpectedNegotiationSubjectCount || $negotiationSubjectCount > $maxExpectedNegotiationSubjectCount) {
        echo "ERROR: Expected between $minExpectedNegotiationSubjectCount and $maxExpectedNegotiationSubjectCount negotiation_subject records, but got $negotiationSubjectCount\n";
        $success = false;
    }

    if ($success) {
        echo "Seeder test completed successfully!\n";
    } else {
        echo "Seeder test failed!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
