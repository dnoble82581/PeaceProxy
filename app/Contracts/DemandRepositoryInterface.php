<?php

namespace App\Contracts;

interface DemandRepositoryInterface
{
    public function createDemand($data);

    public function getDemand($id);

    public function getDemands();

    public function updateDemand($id, $data);

    public function deleteDemand($id);
}
