<?php

namespace App\DTOs\AssessmentQuestionsAnswer;

use Carbon\Carbon;

class AssessmentQuestionsAnswerDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $assessment_id = null,
        public ?int $assessment_template_question_id = null,
        public ?array $answer = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): AssessmentQuestionsAnswerDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['assessment_id'] ?? null,
            $data['assessment_template_question_id'] ?? null,
            $data['answer'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'assessment_id' => $this->assessment_id,
            'assessment_template_question_id' => $this->assessment_template_question_id,
            'answer' => $this->answer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
