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

        $log = $this->addLogEntry($warrant);
        logger($log);

        return $this->warrantRepository->deleteWarrant($warrantId);
    }

    private function addLogEntry(Warrant $warrant)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
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
