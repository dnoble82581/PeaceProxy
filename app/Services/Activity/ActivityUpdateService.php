<?php

namespace App\Services\Activity;

use App\Contracts\ActivityRepositoryInterface;
use App\DTOs\Activity\ActivityDTO;
use App\Models\Activity;

class ActivityUpdateService
{
    protected ActivityRepositoryInterface $activityRepository;

    public function __construct(ActivityRepositoryInterface $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function updateActivity(ActivityDTO $activityDTO, $activityId): ?Activity
    {
        $activity = $this->activityRepository->updateActivity($activityId, $activityDTO->toArray());

        if (!$activity) {
            return null;
        }

        $this->addLogEntry($activity);

        return $activity;
    }

    private function addLogEntry(Activity $activity): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'activity.updated',
            headline: "{$user->name} updated an activity",
            about: $activity,
            by: $user,
            description: str($activity->activity)->limit(140),
            properties: [
                'negotiation_id' => $activity->negotiation_id,
                'subject_id' => $activity->subject_id,
                'type' => $activity->type,
                'is_flagged' => $activity->is_flagged,
            ],
        );
    }
}
