<?php

namespace App\Services\Warning;

use App\Contracts\WarningRepositoryInterface;

class WarningDestructionService
{
    public function __construct(protected WarningRepositoryInterface $warningRepository)
    {
    }

    public function deleteWarning($warningId)
    {
        $this->warningRepository->deleteWarning($warningId);
    }
}
