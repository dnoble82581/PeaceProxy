<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('redirects authenticated users away from the login page to their tenant dashboard', function (): void {
    $tenant = Tenant::factory()->create(['subdomain' => 'alpha']);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    actingAs($user);

    $response = get(route('login'));

    // Expect a redirect to the tenant-aware dashboard route
    $response->assertRedirect(route('dashboard', ['tenantSubdomain' => $tenant->subdomain]));
});


it('uses the tenantSubdomain route parameter first when present for redirects', function (): void {
    $tenant = Tenant::factory()->create(['subdomain' => 'charlie']);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    actingAs($user);

    // Simulate that URL generation defaults are already set for this tenant via middleware
    URL::defaults(['tenantSubdomain' => $tenant->subdomain]);

    // Visiting login while authenticated should respect the tenantSubdomain default and redirect accordingly
    $response = get(route('login'));

    $response->assertRedirect(route('dashboard', ['tenantSubdomain' => $tenant->subdomain]));
});
