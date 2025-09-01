<?php

namespace App\DTOs\RiskAssessment;

use Carbon\Carbon;

class RiskAssessmentDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?int $tenant_id = null,
        public ?int $subject_id = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): RiskAssessmentDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['tenant_id'] ?? null,
            $data['subject_id'] ?? null,
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
            'title' => $this->title,
            'tenant_id' => $this->tenant_id,
            'subject_id' => $this->subject_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
