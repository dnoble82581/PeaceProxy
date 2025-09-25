<?php

namespace App\Services\Activity;

use App\Contracts\ActivityRepositoryInterface;
use App\Models\Activity;

class ActivityDeletionService
{
    protected ActivityRepositoryInterface $activityRepository;

    public function __construct(ActivityRepositoryInterface $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function deleteActivity(int $activityId): ?Activity
    {
        $activity = $this->activityRepository->getActivity($activityId);

        if (! $activity) {
            return null;
        }

        $deleted = $this->activityRepository->deleteActivity($activityId);

        if ($deleted) {
            $this->addLogEntry($activity);
        }

        return $deleted;
    }

    private function addLogEntry(Activity $activity): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'activity.deleted',
            headline: "{$user->name} deleted an activity",
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
