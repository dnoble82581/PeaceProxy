<?php

namespace App\Repositories\MoodLog;

use App\Contracts\MoodLogRepositoryInterface;
use App\Models\moodLog;
use Illuminate\Database\Eloquent\Collection;

class MoodLogRepository implements MoodLogRepositoryInterface
{
    public function createMoodLog($data)
    {
        return moodLog::create($data);
    }

    public function getMoodLogs(): Collection
    {
        return moodLog::all();
    }

    public function getMoodLogsBySubject($subjectId): Collection
    {
        return moodLog::where('subject_id', $subjectId)->get();
    }

    public function updateMoodLog($id, $data)
    {
        $moodLog = $this->getMoodLog($id);
        $moodLog->update($data);
        return $moodLog;
    }

    public function getMoodLog($id)
    {
        return moodLog::find($id);
    }

    public function deleteMoodLog($id)
    {
        $moodLog = $this->getMoodLog($id);
        $moodLog->delete();
        return $moodLog;
    }
}
