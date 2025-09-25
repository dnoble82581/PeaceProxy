<?php

namespace App\Services\Activity;

use App\Contracts\ActivityRepositoryInterface;
use App\DTOs\Activity\ActivityDTO;
use App\Models\Activity;

class ActivityCreationService
{
    protected ActivityRepositoryInterface $activityRepository;

    public function __construct(ActivityRepositoryInterface $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function createActivity(ActivityDTO $activityDTO): Activity
    {
        $newActivity = $this->activityRepository->createActivity($activityDTO->toArray());

        $this->addLogEntry($newActivity);

        return $newActivity;
    }

    private function addLogEntry(Activity $activity): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'activity.created',
            headline: "{$user->name} added an activity",
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
