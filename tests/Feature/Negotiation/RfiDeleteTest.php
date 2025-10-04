<?php

declare(strict_types=1);

use App\Models\{Tenant, User, Negotiation, RequestForInformation, RequestForInformationRecipient};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App as AppFacade;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

function seedRfiForDelete(): array
{
    $tenant = Tenant::factory()->create();

    $sender = User::factory()->create(['tenant_id' => $tenant->id]);
    $recipient = User::factory()->create(['tenant_id' => $tenant->id]);

    AppFacade::instance('currentTenant', $tenant);

    $negotiation = Negotiation::factory()->create(['tenant_id' => $tenant->id]);

    $rfi = RequestForInformation::factory()->create([
        'tenant_id' => $tenant->id,
        'negotiation_id' => $negotiation->id,
        'user_id' => $sender->id,
        'status' => 'Pending',
    ]);

    RequestForInformationRecipient::factory()->create([
        'tenant_id' => $tenant->id,
        'request_for_information_id' => $rfi->id,
        'user_id' => $recipient->id,
        'is_read' => false,
    ]);

    return compact('tenant', 'sender', 'recipient', 'negotiation', 'rfi');
}

it('deletes an RFI safely when the responses modal is open', function () {
    extract(seedRfiForDelete());

    $this->actingAs($sender);

    // Open the responses modal to populate the component with a model in a public property
    $component = Volt::test('pages.negotiation.board.rfi', ['negotiationId' => $negotiation->id])
        ->call('openResponsesModal', $rfi->id);

    // Now delete the same RFI; this previously caused a Livewire model synth error
    $component->call('deleteRfi', $rfi->id)
        ->assertSet('showResponsesModal', false)
        ->assertSet('viewingRfiId', null);

    // Ensure it no longer exists in the database
    $this->assertDatabaseMissing('request_for_information', ['id' => $rfi->id]);
});
