<?php

namespace App\Services\Subject;

use App\DTOs\Subject\SubjectDTO;
use App\Models\Subject;

class SubjectCreationService
{
    public function __construct()
    {
    }

    public function createSubject(SubjectDTO $subjectDTO)
    {
        return Subject::create($subjectDTO->toArray());
    }
}
