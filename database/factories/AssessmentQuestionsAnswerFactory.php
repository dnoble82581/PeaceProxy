<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\AssessmentQuestionsAnswer;
use App\Models\AssessmentTemplateQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssessmentQuestionsAnswerFactory extends Factory{
    protected $model = AssessmentQuestionsAnswer::class;

    public function definition(): array
    {
        return [
            'answer' => $this->faker->words(),//
'created_at' => Carbon::now(),
'updated_at' => Carbon::now(),

'assessment_id' => Assessment::factory(),
'assessment_template_question_id' => AssessmentTemplateQuestion::factory(),
        ];
    }
}
