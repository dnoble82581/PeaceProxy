<?php

namespace App\Services\Warrant;

use App\Contracts\WarrantRepositoryInterface;
use App\DTOs\Warrant\WarrantDTO;

class WarrantUpdatingService
{
    public function __construct(protected WarrantRepositoryInterface $warrantRepository)
    {
    }

    public function updateWarrant(WarrantDTO $warrantDataDTO, $warrantId)
    {
        return $this->warrantRepository->updateWarrant($warrantDataDTO->toArray(), $warrantId);
    }
}
