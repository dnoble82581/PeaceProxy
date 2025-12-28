<?php

declare(strict_types=1);

use Livewire\Volt\Volt;

it('renders the maps component without error', function () {
    // Ensure the MapsStaticService can be resolved and the component renders
    $component = Volt::test('pages.tactical.board.maps');

    $component->assertStatus(200);
});
