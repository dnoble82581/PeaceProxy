<?php

namespace App\Services\MoodLog;

use App\Contracts\MoodLogRepositoryInterface;
use App\Models\moodLog;
use Illuminate\Database\Eloquent\Collection;

class MoodLogFetchingService
{
    public function __construct(protected MoodLogRepositoryInterface $moodLogRepository)
    {
    }

    /**
     * Get a specific mood log by ID.
     */
    public function getMoodLog($id): ?moodLog
    {
        return $this->moodLogRepository->getMoodLog($id);
    }

    /**
     * Get all mood logs.
     */
    public function getMoodLogs(): Collection
    {
        return $this->moodLogRepository->getMoodLogs();
    }

    /**
     * Get all mood logs for a specific subject.
     */
    public function getMoodLogsBySubject($subjectId): Collection
    {
        return $this->moodLogRepository->getMoodLogsBySubject($subjectId);
    }
}
