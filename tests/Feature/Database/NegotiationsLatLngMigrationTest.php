<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('negotiations table has latitude and longitude columns', function (): void {
    expect(Schema::hasColumns('negotiations', ['latitude', 'longitude']))->toBeTrue();
});
