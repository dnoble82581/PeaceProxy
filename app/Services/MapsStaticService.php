<?php

namespace App\Services;

class MapsStaticService
{
    public function incidentImageUrl(array $params): string
    {
        $query = [
            'size' => ($params['width'] ?? 800).'x'.($params['height'] ?? 500),
            'scale' => 2,
            'markers' => $params['markers'] ?? ["{$params['lat']},{$params['lng']}"],
            'key' => config('services.maps.js_key'),
        ];

        if (! empty($params['encodedPolyline'])) {
            $query['path'] = 'enc:'.$params['encodedPolyline'];
        }

        if ($mapId = ($params['mapId'] ?? config('services.maps.map_id'))) {
            $query['map_id'] = $mapId; // optional; remove if not set up for Static Maps
        }

        return 'https://maps.googleapis.com/maps/api/staticmap?'.
            http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }
}
