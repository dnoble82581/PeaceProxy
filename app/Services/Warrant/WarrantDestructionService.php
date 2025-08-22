<?php

namespace App\Services\Warrant;

use App\Contracts\WarrantRepositoryInterface;

class WarrantDestructionService
{
    public function __construct(protected WarrantRepositoryInterface $warrantRepository)
    {
    }

    public function deleteWarrant($warrantId): void
    {
        $this->warrantRepository->deleteWarrant($warrantId);
    }
}
