<?php

namespace App\DTOs\NegotiationSubject;

use App\Enums\Subject\SubjectNegotiationRoles;

class NegotiationSubjectDTO
{
    public function __construct(
        public int $negotiation_id,
        public int $subject_id,
        public SubjectNegotiationRoles $role,
    ) {
    }

    public static function fromArray(array $data): NegotiationSubjectDTO
    {
        return new self(
            $data['negotiation_id'],
            $data['subject_id'],
            isset($data['role']) ? SubjectNegotiationRoles::from($data['role']) : SubjectNegotiationRoles::secondary,
        );
    }

    public function toArray(): array
    {
        return [
            'negotiation_id' => $this->negotiation_id,
            'subject_id' => $this->subject_id,
            'role' => $this->role,
        ];
    }
}
