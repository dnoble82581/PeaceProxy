<?php

declare(strict_types=1);

use App\Models\{Tenant, User, Negotiation, RequestForInformation, RequestForInformationRecipient, RequestForInformationReply};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App as AppFacade;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

function seedRfiScenario(): array
{
    $tenant = Tenant::factory()->create();

    $sender = User::factory()->create(['tenant_id' => $tenant->id]);
    $recipient = User::factory()->create(['tenant_id' => $tenant->id]);
    $bystander = User::factory()->create(['tenant_id' => $tenant->id]);

    // Bind tenant for global scope/tenant() helper
    AppFacade::instance('currentTenant', $tenant);

    $negotiation = Negotiation::factory()->create(['tenant_id' => $tenant->id]);

    $rfi = RequestForInformation::factory()->create([
        'tenant_id' => $tenant->id,
        'negotiation_id' => $negotiation->id,
        'user_id' => $sender->id,
        'status' => 'Pending',
    ]);

    // Attach the intended recipient
    RequestForInformationRecipient::factory()->create([
        'tenant_id' => $tenant->id,
        'request_for_information_id' => $rfi->id,
        'user_id' => $recipient->id,
        'is_read' => false,
    ]);

    return compact('tenant', 'sender', 'recipient', 'bystander', 'negotiation', 'rfi');
}

it('prevents non-recipients or non-creators from replying to an RFI', function () {
    extract(seedRfiScenario());

    $this->actingAs($bystander);

    // Mount the Volt component for the RFI board and open the responses modal
    Volt::test('pages.negotiation.board.rfi', ['negotiationId' => $negotiation->id])
        ->call('openResponsesModal', $rfi->id)
        ->set('replyBody', 'I should not be able to reply')
        ->call('submitReply')
        ->assertHasErrors(['replyBody' => 'You are not authorized to reply to this request.']);

    expect(RequestForInformationReply::query()->where('request_for_information_id', $rfi->id)->count())
        ->toBe(0);
});

it('allows the designated recipient to reply to an RFI', function () {
    extract(seedRfiScenario());

    $this->actingAs($recipient);

    Volt::test('pages.negotiation.board.rfi', ['negotiationId' => $negotiation->id])
        ->call('openResponsesModal', $rfi->id)
        ->set('replyBody', 'Acknowledged. Here is my reply.')
        ->call('submitReply')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('request_for_information_replies', [
        'request_for_information_id' => $rfi->id,
        'user_id' => $recipient->id,
        'body' => 'Acknowledged. Here is my reply.',
        'is_read' => false, // new replies are unread for the other participant
    ]);
});


it('allows the RFI creator to reply to an RFI', function () {
    extract(seedRfiScenario());

    $this->actingAs($sender);

    Volt::test('pages.negotiation.board.rfi', ['negotiationId' => $negotiation->id])
        ->call('openResponsesModal', $rfi->id)
        ->set('replyBody', 'Creator follow-up')
        ->call('submitReply')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('request_for_information_replies', [
        'request_for_information_id' => $rfi->id,
        'user_id' => $sender->id,
        'body' => 'Creator follow-up',
        'is_read' => false,
    ]);
});
