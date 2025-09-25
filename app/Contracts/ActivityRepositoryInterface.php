<?php

namespace App\Contracts;

interface ActivityRepositoryInterface
{
    public function createActivity($data);

    public function getActivity($id);

    public function getActivities();

    public function updateActivity($id, $data);

    public function deleteActivity($id);
}
