<?php

namespace Database\Factories;

use App\Models\Negotiation;
use App\Models\Resource;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ResourceFactory extends Factory
{
    protected $model = Resource::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
            'status' => $this->faker->word(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'meta' => $this->faker->words(),
            'icon' => $this->faker->word(),
            'color' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
            'negotiation_id' => Negotiation::factory(),
        ];
    }
}
