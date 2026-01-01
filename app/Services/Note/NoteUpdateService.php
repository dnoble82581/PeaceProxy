<?php

namespace App\Services\Note;

use App\Contracts\NoteRepositoryInterface;
use App\DTOs\Note\NoteDTO;
use App\Events\Note\NoteUpdatedEvent;
use App\Models\Note;

class NoteUpdateService
{
    protected NoteRepositoryInterface $noteRepository;

    public function __construct(NoteRepositoryInterface $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function updateNote(NoteDTO $noteDTO, $noteId): ?Note
    {
        $note = $this->noteRepository->updateNote($noteId, $noteDTO->toArray());

        if (!$note) {
            return null;
        }

        $this->addLogEntry($note);

        event(new NoteUpdatedEvent($note->id, $note->negotiation_id));

        return $note;
    }

    private function addLogEntry(Note $note): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'note.updated',
            headline: "{$user->name} updated a note",
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
