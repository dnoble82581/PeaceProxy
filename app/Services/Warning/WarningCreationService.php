<?php

namespace App\Services\Warning;

use App\Contracts\WarningRepositoryInterface;
use App\DTOs\Warning\WarningDTO;

class WarningCreationService
{
    protected WarningRepositoryInterface $warningRepository;

    public function __construct(WarningRepositoryInterface $warningRepository)
    {
        $this->warningRepository = $warningRepository;
    }

    public function createWarning(WarningDTO $warningDTO)
    {
        return $this->warningRepository->createWarning($warningDTO->toArray());
    }
}
