<?php

namespace App\Services\Warrant;

use App\Contracts\WarrantRepositoryInterface;
use App\DTOs\Warrant\WarrantDTO;
use App\Models\Warrant;

class WarrantCreationService
{
    public function __construct(protected WarrantRepositoryInterface $warrantRepository)
    {
    }

    public function createWarrant(WarrantDTO $data)
    {
        $warrant = $this->warrantRepository->createWarrant($data->toArray());

        $this->addLogEntry($warrant);

        return $warrant;
    }

    private function addLogEntry(Warrant $warrant): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'warrant.created',
            headline: "{$user->name} created a warrant",
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
