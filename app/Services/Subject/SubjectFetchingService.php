<?php

namespace App\Services\Subject;

use App\Models\Subject;

class SubjectFetchingService
{
    public function __construct()
    {
    }

    public function fetchSubjectById($id)
    {
        return Subject::with([
            'contactPoints',
        ])->find($id);
    }
}
