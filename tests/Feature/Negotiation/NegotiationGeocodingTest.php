<?php

declare(strict_types=1);

use App\Models\Negotiation;
use App\Models\Tenant;
use App\Services\Negotiation\NegotiationCreationService;
use App\Services\Map\GeocodeService;
use Illuminate\Support\Facades\App as AppFacade;

it('geocodes address and stores latitude/longitude on negotiation create', function (): void {
    // Mock GeocodeService to return fixed coordinates
    app()->bind(GeocodeService::class, function () {
        return new class () extends GeocodeService {
            public function geocode(string $address): ?array
            {
                return ['lat' => 41.6611, 'lng' => -91.5302];
            }
        };
    });

    $tenant = Tenant::factory()->create();
    AppFacade::instance('currentTenant', $tenant);

    /** @var NegotiationCreationService $service */
    $service = app(NegotiationCreationService::class);

    $data = [
        'tenant_id' => $tenant->id,
        'title' => 'Test Negotiation With Address',
        'status' => 'active',
        'type' => 'hostage',
        'location_address' => '123 Main St',
        'location_city' => 'Iowa City',
        'location_state' => 'IA',
        'location_zip' => '52240',
    ];

    $negotiation = $service->createNegotiation($data);

    expect($negotiation)->toBeInstanceOf(Negotiation::class);
    expect($negotiation->latitude)->toBeFloat()->toBe(41.6611);
    expect($negotiation->longitude)->toBeFloat()->toBe(-91.5302);

    // Also assert in database
    $stored = Negotiation::query()->whereKey($negotiation->id)->firstOrFail();
    expect($stored->latitude)->toBe(41.6611);
    expect($stored->longitude)->toBe(-91.5302);
});

it('leaves latitude/longitude null when no address provided or geocode fails', function (): void {
    // Mock GeocodeService to return null
    app()->bind(GeocodeService::class, function () {
        return new class () extends GeocodeService {
            public function geocode(string $address): ?array
            {
                return null;
            }
        };
    });

    $tenant = Tenant::factory()->create();
    AppFacade::instance('currentTenant', $tenant);

    /** @var NegotiationCreationService $service */
    $service = app(NegotiationCreationService::class);

    $data = [
        'tenant_id' => $tenant->id,
        'title' => 'Test Negotiation Without Address',
        'status' => 'active',
        'type' => 'hostage',
        // No address fields provided
    ];

    $negotiation = $service->createNegotiation($data);

    expect($negotiation->latitude)->toBeNull();
    expect($negotiation->longitude)->toBeNull();
});
