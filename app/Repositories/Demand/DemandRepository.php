<?php

namespace App\Repositories\Demand;

use App\Contracts\DemandRepositoryInterface;
use App\Models\Demand;
use Illuminate\Database\Eloquent\Collection;

class DemandRepository implements DemandRepositoryInterface
{
    public function createDemand($data)
    {
        return Demand::create($data);
    }

    public function getDemands(): Collection
    {
        return Demand::all();
    }

    public function updateDemand($id, $data)
    {
        $demand = $this->getDemand($id);
        $demand->update($data);
        return $demand;
    }

    public function getDemand($id, ?array $with = [])
    {
        logger($with);
        return Demand::with($with)->find($id);
    }

    public function deleteDemand($id)
    {
        $demand = $this->getDemand($id);
        $demand->delete();
        return $demand;
    }
}
