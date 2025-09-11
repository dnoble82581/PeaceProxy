<?php

namespace App\Services\Note;

use App\Contracts\NoteRepositoryInterface;
use App\DTOs\Note\NoteDTO;
use App\Events\Note\NoteCreatedEvent;
use App\Models\Note;

class NoteCreationService
{
    protected NoteRepositoryInterface $noteRepository;

    public function __construct(NoteRepositoryInterface $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function createNote(NoteDTO $noteDTO): Note
    {
        $newNote = $this->noteRepository->createNote($noteDTO->toArray());

        $log = $this->addLogEntry($newNote);
        logger($log);

        event(new NoteCreatedEvent($newNote->id, $newNote->negotiation_id));
        return $newNote;
    }

    private function addLogEntry(Note $note)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'note.created',
            headline: "{$user->name} created a note",
            about: $note,      // loggable target
            by: $user,            // actor
            description: str($note->title)->limit(140),
            properties: [
                'negotiation_id' => $note->negotiation_id,
                'is_private' => $note->is_private,
                'pinned' => $note->pinned,
            ],
        );
    }
}
