<?php

namespace App\Services\Pin;

use App\Contracts\PinRepositoryInterface;
use App\Models\Pin;
use Illuminate\Database\Eloquent\Collection;

class PinFetchingService
{
    protected PinRepositoryInterface $pinRepository;

    public function __construct(PinRepositoryInterface $pinRepository)
    {
        $this->pinRepository = $pinRepository;
    }

    public function getPin($id): ?Pin
    {
        return $this->pinRepository->getPin($id);
    }

    public function getPins(): Collection
    {
        return $this->pinRepository->getPins();
    }

    public function getPinByPinnable($pinnableType, $pinnableId): ?Pin
    {
        return $this->pinRepository->getPinByPinnable($pinnableType, $pinnableId);
    }

    public function isPinned($pinnableType, $pinnableId): bool
    {
        return $this->getPinByPinnable($pinnableType, $pinnableId) !== null;
    }
}
