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
        event(new NoteCreatedEvent($newNote->id, $newNote->negotiation_id));
        return $newNote;
    }
}
