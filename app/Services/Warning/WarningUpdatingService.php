<?php

namespace App\Services\Warning;

use App\Contracts\WarningRepositoryInterface;
use App\DTOs\Warning\WarningDTO;

class WarningUpdatingService
{
    public function __construct(protected WarningRepositoryInterface $warningRepository)
    {
    }

    public function updateWarning(WarningDTO $warningDataDTO, $warningId)
    {
        return $this->warningRepository->updateWarning($warningId, $warningDataDTO->toArray());
    }
}
