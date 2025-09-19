<?php

namespace Database\Factories;

use App\Models\DeliveryPlan;
use App\Models\Negotiation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DeliveryPlanFactory extends Factory
{
    protected $model = DeliveryPlan::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'summary' => $this->faker->text(),
            'category' => $this->faker->word(),
            'status' => $this->faker->word(),
            'scheduled_at' => Carbon::now(),
            'window_starts_at' => Carbon::now(),
            'window_ends_at' => Carbon::now(),
            'location_name' => $this->faker->name(),
            'location_address' => $this->faker->address(),
            'geo' => $this->faker->words(),
            'route' => $this->faker->words(),
            'instructions' => $this->faker->words(),
            'constraints' => $this->faker->words(),
            'contingencies' => $this->faker->words(),
            'risk_assessment' => $this->faker->words(),
            'signals' => $this->faker->words(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
            'negotiation_id' => Negotiation::factory(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
