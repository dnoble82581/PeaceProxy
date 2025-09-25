<?php

namespace App\Services\Activity;

use App\Contracts\ActivityRepositoryInterface;

class ActivityService
{
    public function __construct(protected ActivityRepositoryInterface $activityRepository)
    {
    }
}
