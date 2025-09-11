<?php

require __DIR__.'/vendor/autoload.php';

use App\DTOs\Subject\SubjectDTO;
use App\Services\Subject\SubjectCreationService;
use App\Services\ContactPoint\ContactPointFetchingService;
use Illuminate\Foundation\Application;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set the tenant ID (you may need to adjust this based on your application)
$tenantId = 1;

// Import the required enums
use App\Enums\Subject\MoodLevels;
use App\Enums\Subject\SubjectNegotiationStatuses;

// Create a new subject with a phone number
$subjectDTO = new SubjectDTO(
    tenant_id: $tenantId,
    name: 'Test Subject',
    phone: '555-123-4567',
    current_mood: MoodLevels::Neutral,
    status: SubjectNegotiationStatuses::active
);

// Create the subject
$subjectCreationService = app(SubjectCreationService::class);
$subject = $subjectCreationService->createSubject($subjectDTO);

echo "Subject created with ID: {$subject->id}\n";

// Fetch the contact points for the subject
$contactPointFetchingService = app(ContactPointFetchingService::class);
$contactPoints = $contactPointFetchingService->getContactPointsBySubjectId($subject->id);

echo "Contact points for subject {$subject->id}:\n";
foreach ($contactPoints as $contactPoint) {
    echo "- Type: {$contactPoint->kind}, Label: {$contactPoint->label}, Primary: " . ($contactPoint->is_primary ? 'Yes' : 'No') . "\n";

    if ($contactPoint->kind === 'phone') {
        $phone = $contactPoint->phone;
        echo "  Phone: {$phone->e164}, Ext: {$phone->ext}, Country: {$phone->country_iso}\n";
    }
}

echo "Test completed successfully.\n";
