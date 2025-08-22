<?php

namespace App\Services\Note;

use App\Contracts\NoteRepositoryInterface;
use App\DTOs\Note\NoteDTO;
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
        return $this->noteRepository->createNote($noteDTO->toArray());
    }
}
