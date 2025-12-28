<?php

namespace App\Observers\Negotiation;

use App\Models\Negotiation;
use App\Services\Map\GeocodeService;

class NegotiationObserver
{
    /**
     * Handle the Negotiation "saving" event.
     * If address fields change (or lat/lng are empty), geocode and set coordinates.
     */
    public function saving(Negotiation $negotiation): void
    {
        // Build full address if any of the parts are present
        $addressParts = array_filter([
            $negotiation->location_address,
            $negotiation->location_city,
            $negotiation->location_state,
            $negotiation->location_zip,
        ], static fn ($v) => filled($v));

        $shouldGeocode = false;

        // Geocode if address fields are dirty
        foreach (['location_address', 'location_city', 'location_state', 'location_zip'] as $field) {
            if ($negotiation->isDirty($field)) {
                $shouldGeocode = true;
                break;
            }
        }

        // Or if lat/lng missing or zero but we have some address
        if (! $shouldGeocode && (! $negotiation->latitude || ! $negotiation->longitude) && ! empty($addressParts)) {
            $shouldGeocode = true;
        }

        if (! $shouldGeocode || empty($addressParts)) {
            return;
        }

        $fullAddress = implode(', ', $addressParts);

        /** @var GeocodeService $geocoder */
        $geocoder = app(GeocodeService::class);
        $coords = $geocoder->geocode($fullAddress);

        if ($coords !== null) {
            $negotiation->latitude = $coords['lat'];
            $negotiation->longitude = $coords['lng'];
        }
    }
}
