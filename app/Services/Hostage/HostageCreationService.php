<?php

namespace App\Services\Hostage;

use App\Contracts\HostageRepositoryInterface;
use App\DTOs\Hostage\HostageDTO;
use App\Events\Hostage\HostageCreatedEvent;
use App\Models\Hostage;

class HostageCreationService
{
    public function __construct(protected HostageRepositoryInterface $hostageRepository)
    {
    }

    /**
     * Create a new hostage using DTO.
     */
    public function createHostage(HostageDTO $hostageDTO, array $images = []): Hostage
    {
        $hostage = $this->hostageRepository->createHostage($hostageDTO->toArray());

        $this->addLogEntry($hostage);

        // Handle image uploads if provided
        if (!empty($images)) {
            $imageService = app(\App\Services\Image\ImageService::class);
            $imageService->uploadImagesForModel(
                $images,
                $hostage,
                'hostages',
                's3_public'
            );

            // Refresh the hostage model to include the newly attached images
            $hostage->refresh();
        }

        // Dispatch event after images are processed
        event(new HostageCreatedEvent($hostage));
        return $hostage;
    }

    private function addLogEntry(Hostage $hostage): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'hostage.created',
            headline: "{$user->name} added a hostage",
            about: $hostage,      // loggable target
            by: $user,            // actor
            description: str($hostage->name)->limit(140),
            properties: [
                'negotiation_id' => $hostage->negotiation_id,
                'is_primary_hostage' => $hostage->is_primary_hostage,
                'risk_level' => $hostage->risk_level?->value,
                'injury_status' => $hostage->injury_status?->value,
            ],
        );
    }
}
