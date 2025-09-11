<?php

namespace App\Services\Note;

use App\Contracts\NoteRepositoryInterface;
use App\DTOs\Note\NoteDTO;
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

        $log = $this->addLogEntry($note);
        logger($log);

        return $note;
    }

    private function addLogEntry(Note $note)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
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
