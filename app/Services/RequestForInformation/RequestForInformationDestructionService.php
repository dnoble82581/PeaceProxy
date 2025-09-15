<?php

namespace App\Services\RequestForInformation;

use App\Contracts\RequestForInformationRepositoryInterface;
use App\Events\Rfi\RfiDeletedEvent;
use App\Models\RequestForInformation;

class RequestForInformationDestructionService
{
    public function __construct(protected RequestForInformationRepositoryInterface $rfiRepository)
    {
    }

    public function deleteRfi($rfiId)
    {
        $rfi = $this->rfiRepository->getRfi($rfiId);

        if (!$rfi) {
            return null;
        }

        $this->addLogEntry($rfi);

        // Store values needed for event before deletion
        $tenantId = $rfi->tenant_id;
        $negotiationId = $rfi->negotiation_id;

        // Delete the RFI
        $result = $this->rfiRepository->deleteRfi($rfiId);

        // Dispatch event after deletion
        event(new RfiDeletedEvent($rfiId, $tenantId, $negotiationId));

        return $result;
    }

    private function addLogEntry(RequestForInformation $rfi): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'rfi.deleted',
            headline: "{$user->name} deleted a request for information",
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
