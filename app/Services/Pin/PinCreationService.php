<?php

namespace App\Services\Pin;

use App\Contracts\PinRepositoryInterface;
use App\DTOs\Pin\PinDTO;
use App\Events\Pin\NotePinnedEvent;
use App\Models\Pin;

class PinCreationService
{
    protected PinRepositoryInterface $pinRepository;

    public function __construct(PinRepositoryInterface $pinRepository)
    {
        $this->pinRepository = $pinRepository;
    }

    public function createPin(PinDTO $pinDTO): Pin
    {
        $pin = $this->pinRepository->createPin($pinDTO->toArray());

        // If this is a note being pinned, dispatch the appropriate event
        if ($pin->pinnable_type === 'App\\Models\\Note') {
            event(new NotePinnedEvent($pin->tenant_id, $pin->pinnable_id));
        }

        return $pin;
    }
}
