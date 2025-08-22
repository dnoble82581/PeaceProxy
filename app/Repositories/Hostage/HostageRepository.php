<?php

namespace App\Repositories\Hostage;

use App\Contracts\HostageRepositoryInterface;
use App\Models\Hostage;
use App\Services\Image\ImageService;
use Illuminate\Database\Eloquent\Collection;

class HostageRepository implements HostageRepositoryInterface
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function createHostage($data)
    {
        return Hostage::create($data);
    }

    public function getHostages(): Collection
    {
        return Hostage::all();
    }

    public function getHostagesByNegotiation($negotiationId): Collection
    {
        return Hostage::where('negotiation_id', $negotiationId)->get();
    }

    public function updateHostage($id, $data)
    {
        $hostage = $this->getHostage($id);
        $hostage->update($data);
        return $hostage;
    }

    public function getHostage($id)
    {
        return Hostage::find($id);
    }

    public function deleteHostage($id)
    {
        // Get the hostage with its images
        $hostage = Hostage::with('images')->find($id);

        if ($hostage) {
            // Delete all images associated with the hostage
            foreach ($hostage->images as $image) {
                $this->imageService->deleteImage($image);
            }

            // Delete the hostage
            $hostage->delete();
        }

        return $hostage;
    }
}
