<?php

namespace Database\Factories;

use App\Models\DeliveryPlan;
use App\Models\DeliveryPlannables;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DeliveryPlannablesFactory extends Factory
{
    protected $model = DeliveryPlannables::class;

    public function definition(): array
    {
        return [
            'plannable' => $this->faker->word(),
            'role' => $this->faker->word(),
            'notes' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'delivery_plan_id' => DeliveryPlan::factory(),
        ];
    }
}
