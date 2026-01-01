<?php

namespace App\Contracts;

interface NoteRepositoryInterface
{
    public function createNote($data);

    public function getNote($id);

    public function getNotes();

    public function getNotesForNegotiation($negotiationId);

    public function updateNote($id, $data);

    public function deleteNote($id);
}
