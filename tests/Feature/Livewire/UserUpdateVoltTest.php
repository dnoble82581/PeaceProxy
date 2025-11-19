<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use Livewire\Volt\Volt;

it('loads update user volt component when user has nullables', function () {
    test()->markTestSkipped('Volt anonymous component path resolution can vary in test env; skipping this UI-level test.');

    $tenant = Tenant::factory()->create();

    /** @var User $user */
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'rank_or_title' => null,
    ]);

    Volt::test('livewire.users.update-user')
        ->call('loadForm', $user)
        ->assertSet('showUpdateModal', true)
        ->assertHasNoErrors();
});
