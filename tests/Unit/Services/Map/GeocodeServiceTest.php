<?php

declare(strict_types=1);

use App\Services\Map\GeocodeService;
use Illuminate\Support\Facades\Http;

it('returns lat and lng on successful geocode', function () {
    Http::fake([
        'https://maps.googleapis.com/maps/api/geocode/json*' => Http::response([
            'status' => 'OK',
            'results' => [
                [
                    'geometry' => [
                        'location' => ['lat' => 41.6611, 'lng' => -91.5302],
                    ],
                ],
            ],
        ], 200),
    ]);

    $svc = app(GeocodeService::class);
    $coords = $svc->geocode('123 Main St, Iowa City, IA');

    expect($coords)->toBeArray()
        ->and($coords['lat'])->toBeFloat()->toBe(41.6611)
        ->and($coords['lng'])->toBeFloat()->toBe(-91.5302);
});

it('returns null when geocoder has zero results', function () {
    Http::fake([
        'https://maps.googleapis.com/maps/api/geocode/json*' => Http::response([
            'status' => 'ZERO_RESULTS',
            'results' => [],
        ], 200),
    ]);

    $svc = app(GeocodeService::class);
    $coords = $svc->geocode('This address does not exist');

    expect($coords)->toBeNull();
});
