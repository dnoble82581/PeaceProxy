<?php

namespace App\Services\Note;

use App\Contracts\NoteRepositoryInterface;
use App\Models\Note;

class NoteDeletionService
{
    protected NoteRepositoryInterface $noteRepository;

    public function __construct(NoteRepositoryInterface $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function deleteNote($id): ?Note
    {
        // Get the note before deleting it
        $note = $this->noteRepository->getNote($id);

        if (!$note) {
            return null;
        }

        $this->addLogEntry($note);

        return $this->noteRepository->deleteNote($id);
    }

    private function addLogEntry(Note $note): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'note.deleted',
            headline: "{$user->name} deleted a note",
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
