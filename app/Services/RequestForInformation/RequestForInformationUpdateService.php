<?php

namespace App\Services\RequestForInformation;

use App\Contracts\RequestForInformationRepositoryInterface;
use App\DTOs\RequestForInformation\RequestForInformationDTO;
use App\Events\Rfi\RfiUpdatedEvent;
use App\Models\RequestForInformation;

class RequestForInformationUpdateService
{
    public function __construct(protected RequestForInformationRepositoryInterface $rfiRepository)
    {
    }

    public function updateRfi($rfiId, RequestForInformationDTO $rfiDTO)
    {
        $rfi = $this->rfiRepository->updateRfi($rfiId, $rfiDTO->toArray());

        $this->addLogEntry($rfi);

        // Dispatch event
        event(new RfiUpdatedEvent($rfi));

        return $rfi;
    }

    private function addLogEntry(RequestForInformation $rfi): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'rfi.updated',
            headline: "{$user->name} updated a request for information",
            about: $rfi,      // loggable target
            by: $user,        // actor
            description: str($rfi->title)->limit(140),
            properties: [
                'negotiation_id' => $rfi->negotiation_id,
                'status' => $rfi->status,
            ],
        );
    }
}
