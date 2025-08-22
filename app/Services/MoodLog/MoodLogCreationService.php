<?php

namespace App\Services\MoodLog;

use App\Contracts\MoodLogRepositoryInterface;
use App\DTOs\MoodLog\MoodLogDTO;
use App\Events\Mood\MoodCreatedEvent;
use App\Models\moodLog;

class MoodLogCreationService
{
    public function __construct(protected MoodLogRepositoryInterface $moodLogRepository)
    {
    }

    /**
     * Create a new mood log using DTO.
     */
    public function createMoodLog(MoodLogDTO $moodLogDTO): moodLog
    {
        $newMood = $this->moodLogRepository->createMoodLog($moodLogDTO->toArray());
        event(new MoodCreatedEvent($newMood));
        return $newMood;
    }
}
