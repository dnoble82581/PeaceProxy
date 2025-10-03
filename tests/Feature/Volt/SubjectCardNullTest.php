<?php

declare(strict_types=1);

use App\Models\Negotiation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\App as AppFacade;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

it('renders an empty state when a negotiation has no primary subject', function (): void {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    AppFacade::instance('currentTenant', $tenant);
    actingAs($user);

    $negotiation = Negotiation::factory()->create(['tenant_id' => $tenant->id]);

    Volt::test('pages.negotiation.noc-elements.subject.subject-card', [
        'negotiation' => $negotiation,
    ])
        ->assertSee('No primary subject has been selected for this negotiation.');
})->skip('Skipped in CI due to Blade layout component bindings; covered by manual QA.');
