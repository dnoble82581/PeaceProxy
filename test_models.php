<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Enums\Subject\SubjectNegotiationRoles;
use App\Enums\User\UserNegotiationStatuses;
use App\Models\Negotiation;
use App\Models\Subject;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

// Set up Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Clear previous test output
echo "Testing Models and Relationships...\n\n";

try {
    // Create a tenant first (since all models belong to a tenant)
    $tenant = Tenant::factory()->create();
    echo "Created tenant: ".$tenant->agency_name."\n\n";

    // Create a negotiation
    $negotiation = Negotiation::factory()->create([
        'tenant_id' => $tenant->id,
    ]);
    echo "Created negotiation: ".$negotiation->title."\n";
    echo "Status: ".$negotiation->status->value."\n";
    echo "Type: ".$negotiation->type->value."\n\n";

    // Create a subject
    $subject = Subject::factory()->create([
        'tenant_id' => $tenant->id,
    ]);
    echo "Created subject: ".$subject->name."\n";
    echo "Current mood: ".$subject->current_mood->value."\n";
    echo "Status: ".$subject->status->value."\n\n";

    // Create a user
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
    ]);
    echo "Created user: ".$user->name."\n";
    echo "Email: ".$user->email."\n\n";

    // Associate the subject with the negotiation
    $negotiation->subjects()->attach($subject->id, [
        'role' => SubjectNegotiationRoles::primary->value,
    ]);
    echo "Associated subject with negotiation as primary\n";

    // Associate the user with the negotiation
    $negotiation->users()->attach($user->id, [
        'role' => 'negotiator',
        'status' => UserNegotiationStatuses::active->value,
        'joined_at' => now(),
    ]);
    echo "Associated user with negotiation as negotiator\n\n";

    // Test retrieving relationships
    echo "Testing relationships...\n";

    // Get subjects for the negotiation
    $subjects = $negotiation->subjects;
    echo "negotiation has ".count($subjects)." subject(s)\n";
    foreach ($subjects as $s) {
        echo "- Subject: ".$s->name.", Role: ".$s->pivot->role->value."\n";
    }

    // Get users for the negotiation
    $users = $negotiation->users;
    echo "negotiation has ".count($users)." user(s)\n";
    foreach ($users as $u) {
        echo "- User: ".$u->name.", Role: ".$u->pivot->role.", Status: ".$u->pivot->status->value."\n";
    }

    // Get negotiations for the subject
    $negotiations = $subject->negotiations;
    echo "Subject is involved in ".count($negotiations)." negotiation(s)\n";
    foreach ($negotiations as $n) {
        echo "- negotiation: ".$n->title.", Role: ".$n->pivot->role->value."\n";
    }

    // Get negotiations for the user
    $negotiations = $user->negotiations;
    echo "User is involved in ".count($negotiations)." negotiation(s)\n";
    foreach ($negotiations as $n) {
        echo "- negotiation: ".$n->title.", Role: ".$n->pivot->role.", Status: ".$n->pivot->status->value."\n";
    }

    echo "\nModel and relationship tests completed successfully!\n";
} catch (Exception $e) {
    echo "Error: ".$e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
}
