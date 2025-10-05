<?php

namespace App\Services\MoodLog;

use App\Contracts\MoodLogRepositoryInterface;
use App\DTOs\MoodLog\MoodLogDTO;
use App\Events\Mood\MoodCreatedEvent;
use App\Models\MoodLog;

class MoodLogCreationService
{
    public function __construct(protected MoodLogRepositoryInterface $moodLogRepository)
    {
    }

    /**
     * Create a new mood log using DTO.
     */
    public function createMoodLog(MoodLogDTO $moodLogDTO): MoodLog
    {
        $newMood = $this->moodLogRepository->createMoodLog($moodLogDTO->toArray());

        $this->addLogEntry($newMood);

        event(new MoodCreatedEvent($newMood));

        return $newMood;
    }

    private function addLogEntry(MoodLog $moodLog)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'moodlog.created',
            headline: "{$user->name} created a mood log",
            about: $moodLog,      // loggable target
            by: $user,            // actor
            description: 'Mood log created for subject',
            properties: [
                'subject_id' => $moodLog->subject_id,
                'logged_by_id' => $moodLog->logged_by_id,
            ],
        );
    }
}
