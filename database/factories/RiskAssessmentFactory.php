<?php

namespace Database\Factories;

use App\Models\RiskAssessment;
use App\Models\Subject;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RiskAssessmentFactory extends Factory
{
    protected $model = RiskAssessment::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
            'subject_id' => Subject::factory(),
        ];
    }
}
