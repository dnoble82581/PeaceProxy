<?php

namespace App\Services\MoodLog;

use App\Contracts\MoodLogRepositoryInterface;
use App\Models\moodLog;

class MoodLogDestructionService
{
    public function __construct(protected MoodLogRepositoryInterface $moodLogRepository)
    {
    }

    /**
     * Delete a mood log by ID.
     */
    public function deleteMoodLog($id): ?moodLog
    {
        return $this->moodLogRepository->deleteMoodLog($id);
    }
}
