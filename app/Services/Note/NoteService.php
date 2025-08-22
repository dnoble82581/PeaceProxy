<?php

namespace App\Services\Note;

use App\Contracts\NoteRepositoryInterface;

class NoteService
{
    public function __construct(protected NoteRepositoryInterface $noteRepository)
    {
    }
}
