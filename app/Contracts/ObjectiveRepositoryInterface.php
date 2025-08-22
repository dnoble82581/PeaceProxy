<?php

namespace App\Contracts;

interface ObjectiveRepositoryInterface
{
    public function createObjective($data);

    public function getObjective($id);

    public function getObjectives();

    public function getObjectivesByNegotiation($negotiationId);

    public function updateObjective($id, $data);

    public function deleteObjective($id);
}
