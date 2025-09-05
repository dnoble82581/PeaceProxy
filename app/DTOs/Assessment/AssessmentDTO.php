<?php

namespace App\DTOs\Assessment;

use Carbon\Carbon;

class AssessmentDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?int $assessment_template_id = null,
        public ?int $negotiation_id = null,
        public ?int $subject_id = null,
        public ?Carbon $started_at = null,
        public ?Carbon $completed_at = null,
        public string $title = 'Assessment',
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): AssessmentDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['assessment_template_id'] ?? null,
            $data['negotiation_id'] ?? null,
            $data['subject_id'] ?? null,
            isset($data['started_at']) ? Carbon::parse($data['started_at']) : null,
            isset($data['completed_at']) ? Carbon::parse($data['completed_at']) : null,
            $data['title'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'assessment_template_id' => $this->assessment_template_id,
            'negotiation_id' => $this->negotiation_id,
            'subject_id' => $this->subject_id,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
