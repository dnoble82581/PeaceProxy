<?php

namespace App\Services\Warning;

use App\Contracts\WarningRepositoryInterface;
use App\DTOs\Warning\WarningDTO;

class WarningUpdateService
{
    protected WarningRepositoryInterface $warningRepository;

    public function __construct(WarningRepositoryInterface $warningRepository)
    {
        $this->warningRepository = $warningRepository;
    }

    public function updateWarning(int $id, WarningDTO $warningDTO)
    {
        return $this->warningRepository->updateWarning($id, $warningDTO->toArray());
    }
}
