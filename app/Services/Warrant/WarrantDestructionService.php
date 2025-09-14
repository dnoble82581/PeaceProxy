<?php

namespace App\Services\Warrant;

use App\Contracts\WarrantRepositoryInterface;

class WarrantDestructionService
{
    public function __construct(protected WarrantRepositoryInterface $warrantRepository)
    {
    }

    public function deleteWarrant($warrantId): ?Warrant
    {
        // Get the warrant before deleting it
        $warrant = $this->warrantRepository->getWarrant($warrantId);

        if (!$warrant) {
            return null;
        }

        $this->addLogEntry($warrant);

        return $this->warrantRepository->deleteWarrant($warrantId);
    }

    private function addLogEntry(Warrant $warrant): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'warrant.deleted',
            headline: "{$user->name} deleted a warrant",
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
