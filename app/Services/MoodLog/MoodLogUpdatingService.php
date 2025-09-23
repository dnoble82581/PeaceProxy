<?php

namespace App\Services\MoodLog;

use App\Contracts\MoodLogRepositoryInterface;
use App\DTOs\MoodLog\MoodLogDTO;
use App\Models\MoodLog;

class MoodLogUpdatingService
{
    public function __construct(protected MoodLogRepositoryInterface $moodLogRepository)
    {
    }

    /**
     * Update a mood log using DTO.
     */
    public function updateMoodLog(MoodLogDTO $moodLogDTO, $moodLogId): ?MoodLog
    {
        $moodLog = $this->moodLogRepository->updateMoodLog($moodLogId, $moodLogDTO->toArray());

        if (!$moodLog) {
            return null;
        }

        $this->addLogEntry($moodLog);


        return $moodLog;
    }

    private function addLogEntry(MoodLog $moodLog)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'moodlog.updated',
            headline: "{$user->name} updated a mood log",
            about: $moodLog,      // loggable target
            by: $user,            // actor
            description: "Mood log updated for subject",
            properties: [
                'subject_id' => $moodLog->subject_id,
                'logged_by_id' => $moodLog->logged_by_id,
            ],
        );
    }
}
