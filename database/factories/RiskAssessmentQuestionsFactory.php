<?php

namespace Database\Factories;

use App\Models\AssessmentQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RiskAssessmentQuestionsFactory extends Factory
{
    protected $model = AssessmentQuestion::class;

    public function definition(): array
    {
        return [
            'question' => $this->faker->word(),
            'type' => $this->faker->word(),
            'category' => $this->faker->word(),
            'is_active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
