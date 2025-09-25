<?php

namespace App\DTOs\Activity;

use App\Enums\Activity\ActivityType;
use Carbon\Carbon;

class ActivityDTO
{
    public function __construct(
        public int $tenant_id,
        public int $negotiation_id,
        public int $user_id,
        public ?int $subject_id,
        public ?ActivityType $type,
        public string $activity,
        public bool $is_flagged = false,
        public ?Carbon $entered_at = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): ActivityDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'],
            $data['negotiation_id'],
            $data['user_id'],
            $data['subject_id'] ?? null,
            $data['type'] ?? null,
            $data['activity'],
            $data['is_flagged'] ?? false,
            isset($data['entered_at']) ? Carbon::parse($data['entered_at']) : null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'negotiation_id' => $this->negotiation_id,
            'user_id' => $this->user_id,
            'subject_id' => $this->subject_id,
            'type' => $this->type,
            'activity' => $this->activity,
            'is_flagged' => $this->is_flagged,
            'entered_at' => $this->entered_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
