<?php

namespace Database\Factories;

use App\Models\Demand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DemandFactory extends Factory
{
    protected $model = Demand::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
