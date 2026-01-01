<?php

declare(strict_types=1);

use App\Models\Negotiation;
use App\Models\Note;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Volt\Volt;

it('only shows notes for the current negotiation', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create([ 'tenant_id' => $tenant->id ]);

    $negA = Negotiation::factory()->create([ 'tenant_id' => $tenant->id ]);
    $negB = Negotiation::factory()->create([ 'tenant_id' => $tenant->id ]);

    // Notes for A
    $noteA1 = Note::factory()->create([
        'tenant_id' => $tenant->id,
        'author_id' => $user->id,
        'negotiation_id' => $negA->id,
        'title' => 'A1',
        'body' => 'Body A1',
    ]);
    $noteA2 = Note::factory()->create([
        'tenant_id' => $tenant->id,
        'author_id' => $user->id,
        'negotiation_id' => $negA->id,
        'title' => 'A2',
        'body' => 'Body A2',
    ]);

    // Notes for B (should NOT appear)
    $noteB1 = Note::factory()->create([
        'tenant_id' => $tenant->id,
        'author_id' => $user->id,
        'negotiation_id' => $negB->id,
        'title' => 'B1',
        'body' => 'Body B1',
    ]);

    // Render the notes board for negotiation A
    Volt::test('pages.negotiation.board.notes', [
        'negotiationId' => $negA->id,
    ])
        ->call('loadNotes')
        ->assertSee('A1')
        ->assertSee('A2')
        ->assertDontSee('B1');
});
