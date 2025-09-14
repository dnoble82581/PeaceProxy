<?php

namespace App\Services\Warrant;

use App\Contracts\WarrantRepositoryInterface;
use App\DTOs\Warrant\WarrantDTO;

class WarrantUpdatingService
{
    public function __construct(protected WarrantRepositoryInterface $warrantRepository)
    {
    }

    public function updateWarrant(WarrantDTO $warrantDataDTO, $warrantId)
    {
        $warrant = $this->warrantRepository->updateWarrant($warrantDataDTO->toArray(), $warrantId);

        if (!$warrant) {
            return null;
        }

        $this->addLogEntry($warrant);

        return $warrant;
    }

    private function addLogEntry(Warrant $warrant): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'warrant.updated',
            headline: "{$user->name} updated a warrant",
            about: $warrant,      // loggable target
            by: $user,            // actor
            description: str($warrant->offense_description)->limit(140),
            properties: [
                'subject_id' => $warrant->subject_id,
                'type' => $warrant->type?->value,
                'status' => $warrant->status?->value,
                'bond_amount' => $warrant->bond_amount,
                'bond_type' => $warrant->bond_type?->value,
            ],
        );
    }
}
