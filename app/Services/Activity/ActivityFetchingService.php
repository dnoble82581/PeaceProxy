<?php

namespace App\Services\Activity;

use App\Contracts\ActivityRepositoryInterface;
use App\DTOs\Activity\ActivityDTO;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Collection;

class ActivityFetchingService
{
    protected ActivityRepositoryInterface $activityRepository;

    public function __construct(ActivityRepositoryInterface $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function getActivity($id): ?Activity
    {
        return $this->activityRepository->getActivity($id);
    }

    public function getActivities(): Collection
    {
        return $this->activityRepository->getActivities();
    }

    public function getActivityDTO($activityId): ?ActivityDTO
    {
        $activity = $this->getActivity($activityId);

        if (! $activity) {
            return null;
        }

        return ActivityDTO::fromArray($activity->toArray());
    }
}
