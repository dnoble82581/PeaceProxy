<?php

namespace Database\Factories;

use App\Models\ContactPoint;
use App\Models\Subject;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ContactPointFactory extends Factory
{
    protected $model = ContactPoint::class;

    public function definition(): array
    {
        return [
            'kind' => $this->faker->word(),
            'label' => $this->faker->word(),
            'is_primary' => $this->faker->boolean(),
            'is_verified' => $this->faker->boolean(),
            'verified_at' => Carbon::now(),
            'priority' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'contactable_id' => Subject::factory(),
            'contactable_type' => Subject::class,
            'tenant_id' => Tenant::factory(),
        ];
    }
}
