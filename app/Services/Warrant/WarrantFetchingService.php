<?php

namespace App\Services\Warrant;

use App\Contracts\WarrantRepositoryInterface;

class WarrantFetchingService
{
    public function __construct(protected WarrantRepositoryInterface $warrantRepository)
    {
    }

    public function getWarrantById($warrantId)
    {
        return $this->warrantRepository->getWarrant($warrantId);
    }
}
