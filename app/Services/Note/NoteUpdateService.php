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
        return $this->noteRepository->updateNote($noteId, $noteDTO->toArray());
    }
}
