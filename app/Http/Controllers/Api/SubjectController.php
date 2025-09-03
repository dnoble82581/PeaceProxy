<?php

namespace App\Http\Controllers\Api;

use App\Models\Subject;

class SubjectController
{
    public function index()
    {

        $subjects = Subject::with('images')->get();

        // Debug the raw query and its results
        logger($subjects->toArray());

        return response()->json($subjects->map(function ($subject) {
            return [
                'value' => $subject->id,
                'label' => $subject->name,
                'image' => $subject->primaryImage(),
            ];
        }));

    }
}
