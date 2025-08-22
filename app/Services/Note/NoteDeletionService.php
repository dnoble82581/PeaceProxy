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
        return $this->noteRepository->deleteNote($id);
    }
}
