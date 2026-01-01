<?php

namespace App\Repositories\Note;

use App\Contracts\NoteRepositoryInterface;
use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;

class NoteRepository implements NoteRepositoryInterface
{
    public function createNote($data)
    {
        return Note::create($data);
    }

    public function getNotes(): Collection
    {
        return Note::orderBy('created_at', 'desc')->get();
    }

    public function getNotesForNegotiation($negotiationId): Collection
    {
        return Note::where('negotiation_id', $negotiationId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updateNote($id, $data)
    {
        $note = $this->getNote($id);
        $note->update($data);
        return $note;
    }

    public function getNote($id)
    {
        return Note::find($id);
    }

    public function deleteNote($id)
    {
        $note = $this->getNote($id);
        $note->delete();
        return $note;
    }
}
