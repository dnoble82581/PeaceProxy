<?php

namespace App\Repositories\Activity;

use App\Contracts\ActivityRepositoryInterface;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Collection;

class ActivityRepository implements ActivityRepositoryInterface
{
    public function createActivity($data)
    {
        return Activity::create($data);
    }

    public function getActivities(): Collection
    {
        return Activity::orderByDesc('entered_at')
            ->orderByDesc('created_at')
            ->get();
    }

    public function updateActivity($id, $data)
    {
        $activity = $this->getActivity($id);
        if (!$activity) {
            return null;
        }
        $activity->update($data);
        return $activity;
    }

    public function getActivity($id)
    {
        return Activity::find($id);
    }

    public function deleteActivity($id)
    {
        $activity = $this->getActivity($id);
        if (!$activity) {
            return null;
        }
        $activity->delete();
        return $activity;
    }
}
