<?php

namespace App\DTOs\RiskAssessment;

use App\Enums\Assessment\QuestionResponseTypes;
use Carbon\Carbon;

class RiskAssessmentQuestionsDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $negotiation_id = null,
        public ?int $created_by_id = null,
        public ?int $tenant_id = null,
        public ?int $risk_assessment_id = null,
        public ?string $question = null,
        public QuestionResponseTypes|string|null $type = QuestionResponseTypes::text->value,
        public ?string $category = null,
        public ?bool $is_active = true,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
        // Convert string values to enum instances if needed
        if (is_string($this->type) && $this->type !== null) {
            $this->type = QuestionResponseTypes::from($this->type);
        }
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): RiskAssessmentQuestionsDTO
    {
        // The constructor will handle converting string values to enum instances
        return new self(
            $data['id'] ?? null,
            $data['negotiation_id'] ?? null,
            $data['created_by_id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['risk_assessment_id'] ?? null,
            $data['question'] ?? null,
            $data['type'] ?? QuestionResponseTypes::text->value,
            $data['category'] ?? null,
            $data['is_active'] ?? true,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'negotiation_id' => $this->negotiation_id,
            'created_by_id' => $this->created_by_id,
            'tenant_id' => $this->tenant_id,
            'risk_assessment_id' => $this->risk_assessment_id,
            'question' => $this->question,
            'type' => $this->type instanceof QuestionResponseTypes ? $this->type->value : $this->type,
            'category' => $this->category,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
