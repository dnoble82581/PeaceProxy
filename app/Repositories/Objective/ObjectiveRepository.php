<?php

namespace App\Repositories\Objective;

use App\Contracts\ObjectiveRepositoryInterface;
use App\Models\Objective;
use Illuminate\Database\Eloquent\Collection;

class ObjectiveRepository implements ObjectiveRepositoryInterface
{
    public function createObjective($data)
    {
        return Objective::create($data);
    }

    public function getObjectives(): Collection
    {
        return Objective::all();
    }

    public function updateObjective($id, $data)
    {
        $objective = $this->getObjective($id);
        $objective->update($data);
        return $objective;
    }

    public function getObjective($id)
    {
        return Objective::findOrFail($id);
    }

    public function deleteObjective($id)
    {
        return Objective::destroy($id);
    }

    public function getObjectivesByNegotiation($negotiationId): Collection
    {
        return Objective::where('negotiation_id', $negotiationId)->get();
    }
}
