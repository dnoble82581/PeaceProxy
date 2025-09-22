<?php

namespace App\Services\Warrant;

use App\Contracts\WarrantRepositoryInterface;
use App\DTOs\Warrant\WarrantDTO;
use App\Events\Warrant\WarrantCreatedEvent;
use App\Models\Warrant;

class WarrantCreationService
{
    public function __construct(protected WarrantRepositoryInterface $warrantRepository)
    {
    }

    public function createWarrant(WarrantDTO $data)
    {
        $warrant = $this->warrantRepository->createWarrant($data->toArray());

        event(new WarrantCreatedEvent($warrant->subject_id, $warrant->id));

        $this->addLogEntry($warrant);

        return $warrant;
    }

    private function addLogEntry(Warrant $warrant)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
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
