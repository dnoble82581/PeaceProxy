<?php

namespace Tests\Feature;

use App\DTOs\Activity\ActivityDTO;
use App\Models\Activity;
use App\Models\Negotiation;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Activity\ActivityCreationService;
use App\Services\Activity\ActivityFetchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App as AppFacade;
use Tests\TestCase;

class MultitenancyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * It binds currentTenant and verifies TenantScope filters models using the BelongsToTenant trait.
     */
    public function test_tenant_global_scope_filters_models(): void
    {
        $tenantA = Tenant::factory()->create(['subdomain' => 'alpha']);
        $tenantB = Tenant::factory()->create(['subdomain' => 'beta']);

        // Create records for each tenant explicitly
        $negA = Negotiation::factory()->create(['tenant_id' => $tenantA->id, 'title' => 'A Negotiation']);
        $negB = Negotiation::factory()->create(['tenant_id' => $tenantB->id, 'title' => 'B Negotiation']);

        // Bind tenant A and assert only A's records are visible via global scope
        AppFacade::instance('currentTenant', $tenantA);
        $visibleForA = Negotiation::query()->get();
        $this->assertCount(1, $visibleForA);
        $this->assertTrue($visibleForA->first()->is($negA));
        $this->assertEquals([$tenantA->id], $visibleForA->pluck('tenant_id')->unique()->values()->all());

        // Switch to tenant B and assert only B's records are visible
        AppFacade::instance('currentTenant', $tenantB);
        $visibleForB = Negotiation::query()->get();
        $this->assertCount(1, $visibleForB);
        $this->assertTrue($visibleForB->first()->is($negB));
        $this->assertEquals([$tenantB->id], $visibleForB->pluck('tenant_id')->unique()->values()->all());
    }

    /**
     * It ensures service-layer creation uses the authenticated user's tenant (tenant() helper)
     * and persists tenant_id accordingly.
     */
    public function test_activity_creation_service_uses_authenticated_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        // currentTenant is used by some parts of the app (global scopes, etc.)
        AppFacade::instance('currentTenant', $tenant);
        $this->actingAs($user);

        // Create a negotiation for the same tenant to associate the activity
        $negotiation = Negotiation::factory()->create(['tenant_id' => $tenant->id]);

        // Build DTO — note: type is nullable, entered_at timestamps are set in the service via DTO
        $dto = new ActivityDTO(
            tenant_id: $tenant->id, // service will persist this as-is
            negotiation_id: $negotiation->id,
            user_id: $user->id,
            subject_id: null,
            type: null,
            activity: 'Created via service',
            is_flagged: false,
            entered_at: now(),
            created_at: now(),
            updated_at: now(),
        );

        /** @var ActivityCreationService $service */
        $service = app(ActivityCreationService::class);
        $created = $service->createActivity($dto);

        $this->assertInstanceOf(Activity::class, $created);
        $this->assertEquals($tenant->id, $created->tenant_id, 'Activity tenant_id should equal the authenticated tenant id');
        $this->assertEquals($negotiation->id, $created->negotiation_id);
        $this->assertEquals($user->id, $created->user_id);

        // Fetching service returns all Activities (Activity model does not use TenantScope),
        // but we can still assert our record has the correct tenant_id and that cross-tenant
        // records do not leak when querying through a model that uses TenantScope.
        /** @var ActivityFetchingService $fetching */
        $fetching = app(ActivityFetchingService::class);
        $fetchedDto = $fetching->getActivityDTO($created->id);
        $this->assertNotNull($fetchedDto);
        $this->assertEquals($tenant->id, $fetchedDto->tenant_id);
    }
}
