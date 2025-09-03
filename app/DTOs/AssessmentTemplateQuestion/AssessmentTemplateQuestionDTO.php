<?php

namespace App\DTOs\AssessmentTemplateQuestion;

use Carbon\Carbon;

class AssessmentTemplateQuestionDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $assessment_template_id = null,
        public ?string $question = null,
        public ?string $question_type = null,
        public ?string $question_category = null,
        public ?array $options = null,
        public ?bool $is_required = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): AssessmentTemplateQuestionDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['assessment_template_id'] ?? null,
            $data['question'] ?? null,
            $data['question_type'] ?? null,
            $data['question_category'] ?? null,
            $data['options'] ?? null,
            isset($data['is_required']) ? (bool) $data['is_required'] : null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'assessment_template_id' => $this->assessment_template_id,
            'question' => $this->question,
            'question_type' => $this->question_type,
            'question_category' => $this->question_category,
            'options' => $this->options,
            'is_required' => $this->is_required,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
