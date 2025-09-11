<?php

namespace App\Services\MoodLog;

use App\Contracts\MoodLogRepositoryInterface;
use App\Models\MoodLog;

class MoodLogDestructionService
{
    public function __construct(protected MoodLogRepositoryInterface $moodLogRepository)
    {
    }

    /**
     * Delete a mood log by ID.
     */
    public function deleteMoodLog($id): ?MoodLog
    {
        // Get the mood log before deleting it
        $moodLog = $this->moodLogRepository->getMoodLog($id);

        if (!$moodLog) {
            return null;
        }

        $log = $this->addLogEntry($moodLog);
        logger($log);

        $deletedMoodLog = $this->moodLogRepository->deleteMoodLog($id);
        event(new MoodDeletedEvent($moodLog));

        return $deletedMoodLog;
    }

    private function addLogEntry(MoodLog $moodLog)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'moodlog.deleted',
            headline: "{$user->name} deleted a mood log",
            about: $moodLog,      // loggable target
            by: $user,            // actor
            description: "Mood log deleted for subject",
            properties: [
                'subject_id' => $moodLog->subject_id,
                'logged_by_id' => $moodLog->logged_by_id,
            ],
        );
    }
}
