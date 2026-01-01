<?php

declare(strict_types=1);

use App\DTOs\Note\NoteDTO;
use App\Events\Note\NoteCreatedEvent;
use App\Events\Note\NoteDeletedEvent;
use App\Events\Note\NoteUpdatedEvent;
use App\Models\Negotiation;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Note\NoteCreationService;
use App\Services\Note\NoteDeletionService;
use App\Services\Note\NoteUpdateService;
use Illuminate\Support\Facades\Event;

it('dispatches note created/updated/deleted events via services', function () {
    Event::fake();

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create([ 'tenant_id' => $tenant->id ]);
    $negotiation = Negotiation::factory()->create([ 'tenant_id' => $tenant->id ]);

    // Authenticate context for services that log using auth()
    $this->be($user);

    // Create
    $createDto = new NoteDTO(
        id: null,
        negotiation_id: $negotiation->id,
        tenant_id: $tenant->id,
        author_id: $user->id,
        title: 'First',
        body: 'Body',
        is_private: false,
        pinned: false,
    );

    $note = app(NoteCreationService::class)->createNote($createDto);

    Event::assertDispatched(NoteCreatedEvent::class, function (NoteCreatedEvent $e) use ($negotiation, $note) {
        return $e->negotiationId === $negotiation->id && $e->noteId === $note->id;
    });

    // Update
    $updateDto = new NoteDTO(
        id: $note->id,
        negotiation_id: $negotiation->id,
        tenant_id: $tenant->id,
        author_id: $user->id,
        title: 'Updated',
        body: 'Updated body',
        is_private: false,
        pinned: false,
    );

    app(NoteUpdateService::class)->updateNote($updateDto, $note->id);

    Event::assertDispatched(NoteUpdatedEvent::class, function (NoteUpdatedEvent $e) use ($negotiation, $note) {
        return $e->negotiationId === $negotiation->id && $e->noteId === $note->id;
    });

    // Delete
    app(NoteDeletionService::class)->deleteNote($note->id);

    Event::assertDispatched(NoteDeletedEvent::class, function (NoteDeletedEvent $e) use ($negotiation, $note) {
        return $e->negotiationId === $negotiation->id && $e->noteId === $note->id;
    });
});
