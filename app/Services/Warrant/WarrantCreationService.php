<?php

namespace App\Services\Warrant;

use App\Contracts\WarrantRepositoryInterface;
use App\DTOs\Warrant\WarrantDTO;

class WarrantCreationService
{
    public function __construct(protected WarrantRepositoryInterface $warrantRepository)
    {
    }

    public function createWarrant(WarrantDTO $data)
    {
        return $this->warrantRepository->createWarrant($data->toArray());
    }
}
