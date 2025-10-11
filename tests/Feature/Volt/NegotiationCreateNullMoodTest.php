<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Negotiation;
use App\Models\Subject;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('creates negotiation and subject when mood is null without error', function () {
    // Create and authenticate a user (tenant is auto-created via factory/booted)
    $user = User::factory()->create();
    actingAs($user);

    $title = 'Test Negotiation With Null Mood';

    // Mount the Volt component and submit with minimal required data
    Volt::test('forms.negotiation.create')
        ->set('negotiationForm.title', $title)
        ->set('subjectName', 'Jane Smith')
        // Ensure integer field is null to avoid DB casting issues in test
        ->set('negotiationForm.location_zip', null)
        // Leave current_mood as null on purpose
        ->call('saveNegotiation')
        ->assertHasNoErrors();

    // Ensure negotiation and subject were created
    assertDatabaseHas('negotiations', [
        'title' => $title,
        'tenant_id' => $user->tenant_id,
    ]);

    assertDatabaseHas('subjects', [
        'name' => 'Jane Smith',
        'tenant_id' => $user->tenant_id,
    ]);
});
