<?php

namespace App\Services\Warning;

use App\Contracts\WarningRepositoryInterface;

class WarningDeletionService
{
    protected WarningRepositoryInterface $warningRepository;

    public function __construct(WarningRepositoryInterface $warningRepository)
    {
        $this->warningRepository = $warningRepository;
    }

    public function deleteWarning(int $id): void
    {
        $this->warningRepository->deleteWarning($id);
    }
}
