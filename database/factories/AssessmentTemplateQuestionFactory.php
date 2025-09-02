<?php

namespace Database\Factories;

use App\Models\AssessmentTemplateQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssessmentTemplateQuestionFactory extends Factory{
    protected $model = AssessmentTemplateQuestion::class;

    public function definition(): array
    {
        return [
            'question' => $this->faker->word(),//
'question_type' => $this->faker->word(),
'question_category' => $this->faker->word(),
'created_at' => Carbon::now(),
'updated_at' => Carbon::now(),
        ];
    }
}
