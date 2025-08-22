<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\Tenant;
use App\Models\Warning;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WarningFactory extends Factory
{
    protected $model = Warning::class;

    public function definition(): array
    {
        return [
            'warning_type' => $this->faker->word(),
            'warning' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'subject_id' => Subject::factory(),
            'tenant_id' => Tenant::factory(),
        ];
    }
}
