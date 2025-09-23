<?php

namespace App\Services\RequestForInformation;

use App\Contracts\RequestForInformationRepositoryInterface;
use App\DTOs\RequestForInformation\RequestForInformationDTO;
use App\Events\Rfi\RfiCreatedEvent;
use App\Models\RequestForInformation;

class RequestForInformationCreationService
{
    public function __construct(protected RequestForInformationRepositoryInterface $rfiRepository)
    {
    }

    public function createRfi(RequestForInformationDTO $rfiDTO)
    {
        $rfi = $this->rfiRepository->createRfi($rfiDTO->toArray());

        $this->addLogEntry($rfi);

        // Dispatch event
        event(new RfiCreatedEvent($rfi->negotiation_id, $rfi->id));

        return $rfi;
    }

    private function addLogEntry(RequestForInformation $rfi): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'rfi.created',
            headline: "{$user->name} created a request for information",
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
