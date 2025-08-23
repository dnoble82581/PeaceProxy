<?php

namespace App\Services\Pin;

use App\Contracts\PinRepositoryInterface;
use App\Events\Pin\NoteUnpinnedEvent;
use App\Models\Pin;

class PinDeletionService
{
    protected PinRepositoryInterface $pinRepository;

    public function __construct(PinRepositoryInterface $pinRepository)
    {
        $this->pinRepository = $pinRepository;
    }

    public function deletePin($id): ?Pin
    {
        $pin = $this->pinRepository->getPin($id);

        if ($pin) {
            $tenantId = $pin->tenant_id;
            $pinnableId = $pin->pinnable_id;
            $pinnableType = $pin->pinnable_type;

            $this->pinRepository->deletePin($id);

            // If this was a note being unpinned, dispatch the appropriate event
            if ($pinnableType === 'App\\Models\\Note') {
                event(new NoteUnpinnedEvent($tenantId, $pinnableId));
            }
        }

        return $pin;
    }

    public function deletePinByPinnable($pinnableType, $pinnableId): ?Pin
    {
        $pin = $this->pinRepository->getPinByPinnable($pinnableType, $pinnableId);

        if ($pin) {
            $tenantId = $pin->tenant_id;

            $this->pinRepository->deletePinByPinnable($pinnableType, $pinnableId);

            // If this was a note being unpinned, dispatch the appropriate event
            if ($pinnableType === 'App\\Models\\Note') {
                event(new NoteUnpinnedEvent($tenantId, $pinnableId));
            }
        }

        return $pin;
    }
}
