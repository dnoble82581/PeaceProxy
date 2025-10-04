<?php

namespace Database\Factories;

use App\Models\Negotiation;
use App\Models\Subject;
use App\Models\SubjectSorCheck;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SubjectSorCheckFactory extends Factory
{
    protected $model = SubjectSorCheck::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->word(),
            'result_count' => $this->faker->word(),
            'matches' => $this->faker->words(),
            'error' => $this->faker->word(),
            'checked_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'subject_id' => Subject::factory(),
            'tenant_id' => Tenant::factory(),
            'negotiation_id' => Negotiation::factory(),
        ];
    }
}
