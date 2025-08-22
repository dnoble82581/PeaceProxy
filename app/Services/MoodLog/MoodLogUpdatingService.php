<?php

namespace App\Services\MoodLog;

use App\Contracts\MoodLogRepositoryInterface;
use App\DTOs\MoodLog\MoodLogDTO;
use App\Models\moodLog;

class MoodLogUpdatingService
{
    public function __construct(protected MoodLogRepositoryInterface $moodLogRepository)
    {
    }

    /**
     * Update a mood log using DTO.
     */
    public function updateMoodLog(MoodLogDTO $moodLogDTO, $moodLogId): ?moodLog
    {
        return $this->moodLogRepository->updateMoodLog($moodLogId, $moodLogDTO->toArray());
    }
}
