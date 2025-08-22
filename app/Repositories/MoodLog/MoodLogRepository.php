<?php

namespace App\Repositories\MoodLog;

use App\Contracts\MoodLogRepositoryInterface;
use App\Models\MoodLog;
use Illuminate\Database\Eloquent\Collection;

class MoodLogRepository implements MoodLogRepositoryInterface
{
    public function createMoodLog($data)
    {
        return MoodLog::create($data);
    }

    public function getMoodLogs(): Collection
    {
        return MoodLog::all();
    }

    public function getMoodLogsBySubject($subjectId): Collection
    {
        return MoodLog::where('subject_id', $subjectId)->get();
    }

    public function updateMoodLog($id, $data)
    {
        $moodLog = $this->getMoodLog($id);
        $moodLog->update($data);
        return $moodLog;
    }

    public function getMoodLog($id)
    {
        return MoodLog::find($id);
    }

    public function deleteMoodLog($id)
    {
        $moodLog = $this->getMoodLog($id);
        $moodLog->delete();
        return $moodLog;
    }
}
