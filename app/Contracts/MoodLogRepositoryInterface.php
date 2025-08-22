<?php

namespace App\Contracts;

interface MoodLogRepositoryInterface
{
    public function createMoodLog($data);

    public function getMoodLog($id);

    public function getMoodLogs();

    public function getMoodLogsBySubject($subjectId);

    public function updateMoodLog($id, $data);

    public function deleteMoodLog($id);
}
