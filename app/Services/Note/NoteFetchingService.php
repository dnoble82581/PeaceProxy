<?php

namespace App\Services\Note;

use App\Contracts\NoteRepositoryInterface;
use App\DTOs\Note\NoteDTO;
use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;

class NoteFetchingService
{
    protected NoteRepositoryInterface $noteRepository;

    public function __construct(NoteRepositoryInterface $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function getNote($id): ?Note
    {
        return $this->noteRepository->getNote($id);
    }

    public function getNotes(): Collection
    {
        return $this->noteRepository->getNotes();
    }

    public function getNotesForNegotiation(int $negotiationId): Collection
    {
        return $this->noteRepository->getNotesForNegotiation($negotiationId);
    }

    public function getNoteDTO($noteId): ?NoteDTO
    {
        $note = $this->getNote($noteId);

        if (! $note) {
            return null;
        }

        return NoteDTO::fromArray($note->toArray());
    }
}
