<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Enums\Negotiation\NegotiationStatuses;
use App\Enums\Negotiation\NegotiationTypes;
use App\Enums\Subject\MoodLevels;
use App\Enums\Subject\SubjectNegotiationRoles;
use App\Enums\Subject\SubjectNegotiationStatuses;
use App\Enums\User\UserNegotiationStatuses;
use App\Models\Negotiation;
use App\Models\NegotiationSubject;
use App\Models\NegotiationUser;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

// Set up Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Clear previous test output
echo "Testing Models and Relationships Structure...\n\n";

try {
    // Verify model structure
    echo "Verifying model structure...\n";

    // Check negotiation model
    echo "negotiation model:\n";
    echo "- Has HasFactory trait: ".(method_exists(Negotiation::class, 'factory') ? "Yes" : "No")."\n";
    echo "- Has BelongsToTenant trait: ".(in_array(
        'App\Traits\BelongsToTenant',
        class_uses_recursive(Negotiation::class)
    ) ? "Yes" : "No")."\n";
    echo "- Has users() relationship: ".(method_exists(Negotiation::class, 'users') ? "Yes" : "No")."\n";
    echo "- Has subjects() relationship: ".(method_exists(Negotiation::class, 'subjects') ? "Yes" : "No")."\n";
    echo "- Has tenant() relationship: ".(method_exists(Negotiation::class, 'tenant') ? "Yes" : "No")."\n\n";

    // Check Subject model
    echo "Subject model:\n";
    echo "- Has HasFactory trait: ".(method_exists(Subject::class, 'factory') ? "Yes" : "No")."\n";
    echo "- Has BelongsToTenant trait: ".(in_array(
        'App\Traits\BelongsToTenant',
        class_uses_recursive(Subject::class)
    ) ? "Yes" : "No")."\n";
    echo "- Has negotiations() relationship: ".(method_exists(Subject::class, 'negotiations') ? "Yes" : "No")."\n\n";

    // Check User model
    echo "User model:\n";
    echo "- Has HasFactory trait: ".(method_exists(User::class, 'factory') ? "Yes" : "No")."\n";
    echo "- Has BelongsToTenant trait: ".(in_array(
        'App\Traits\BelongsToTenant',
        class_uses_recursive(User::class)
    ) ? "Yes" : "No")."\n";
    echo "- Has negotiations() relationship: ".(method_exists(User::class, 'negotiations') ? "Yes" : "No")."\n\n";

    // Check pivot models
    echo "NegotiationUser model:\n";
    echo "- Extends Pivot: ".(is_subclass_of(
        NegotiationUser::class,
        'Illuminate\Database\Eloquent\Relations\Pivot'
    ) ? "Yes" : "No")."\n";
    echo "- Has negotiation() relationship: ".(method_exists(
        NegotiationUser::class,
        'negotiation'
    ) ? "Yes" : "No")."\n";
    echo "- Has user() relationship: ".(method_exists(NegotiationUser::class, 'user') ? "Yes" : "No")."\n\n";

    echo "NegotiationSubject model:\n";
    echo "- Extends Pivot: ".(is_subclass_of(
        NegotiationSubject::class,
        'Illuminate\Database\Eloquent\Relations\Pivot'
    ) ? "Yes" : "No")."\n";
    echo "- Has negotiation() relationship: ".(method_exists(
        NegotiationSubject::class,
        'negotiation'
    ) ? "Yes" : "No")."\n";
    echo "- Has subject() relationship: ".(method_exists(
        NegotiationSubject::class,
        'subject'
    ) ? "Yes" : "No")."\n\n";

    // Verify factory structure
    echo "Verifying factory structure...\n";

    // Check NegotiationFactory
    echo "NegotiationFactory:\n";
    echo "- Factory exists: ".(class_exists('Database\Factories\NegotiationFactory') ? "Yes" : "No")."\n";

    // Check SubjectFactory
    echo "SubjectFactory:\n";
    echo "- Factory exists: ".(class_exists('Database\Factories\SubjectFactory') ? "Yes" : "No")."\n\n";

    // Check NegotiationUserFactory
    echo "NegotiationUserFactory:\n";
    echo "- Factory exists: ".(class_exists('Database\Factories\NegotiationUserFactory') ? "Yes" : "No")."\n\n";

    // Check NegotiationSubjectFactory
    echo "NegotiationSubjectFactory:\n";
    echo "- Factory exists: ".(class_exists('Database\Factories\NegotiationSubjectFactory') ? "Yes" : "No")."\n\n";

    // Verify enum structure
    echo "Verifying enum structure...\n";

    // Check NegotiationStatuses enum
    echo "NegotiationStatuses enum:\n";
    echo "- Enum exists: ".(class_exists(NegotiationStatuses::class) ? "Yes" : "No")."\n";
    echo "- Has cases: ".(!empty(NegotiationStatuses::cases()) ? "Yes" : "No")."\n\n";

    // Check NegotiationTypes enum
    echo "NegotiationTypes enum:\n";
    echo "- Enum exists: ".(class_exists(NegotiationTypes::class) ? "Yes" : "No")."\n";
    echo "- Has cases: ".(!empty(NegotiationTypes::cases()) ? "Yes" : "No")."\n\n";

    // Check SubjectNegotiationRoles enum
    echo "SubjectNegotiationRoles enum:\n";
    echo "- Enum exists: ".(class_exists(SubjectNegotiationRoles::class) ? "Yes" : "No")."\n";
    echo "- Has cases: ".(!empty(SubjectNegotiationRoles::cases()) ? "Yes" : "No")."\n\n";

    // Check SubjectNegotiationStatuses enum
    echo "SubjectNegotiationStatuses enum:\n";
    echo "- Enum exists: ".(class_exists(SubjectNegotiationStatuses::class) ? "Yes" : "No")."\n";
    echo "- Has cases: ".(!empty(SubjectNegotiationStatuses::cases()) ? "Yes" : "No")."\n\n";

    // Check MoodLevels enum
    echo "MoodLevels enum:\n";
    echo "- Enum exists: ".(class_exists(MoodLevels::class) ? "Yes" : "No")."\n";
    echo "- Has cases: ".(!empty(MoodLevels::cases()) ? "Yes" : "No")."\n\n";

    // Check UserNegotiationStatuses enum
    echo "UserNegotiationStatuses enum:\n";
    echo "- Enum exists: ".(class_exists(UserNegotiationStatuses::class) ? "Yes" : "No")."\n";
    echo "- Has cases: ".(!empty(UserNegotiationStatuses::cases()) ? "Yes" : "No")."\n\n";

    echo "Model and relationship structure tests completed successfully!\n";
} catch (Exception $e) {
    echo "Error: ".$e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
}
