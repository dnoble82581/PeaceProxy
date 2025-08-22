<?php

namespace App\DTOs\Objective;

use App\Enums\Objective\Priority;
use Carbon\Carbon;

class ObjectiveDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?int $negotiation_id = null,
        public ?int $created_by_id = null,
        public Priority|string|null $priority = 'low',
        public ?string $status = 'In Progress',
        public ?string $objective = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
        // Convert string values to enum instances if needed
        if (is_string($this->priority) && $this->priority !== null) {
            $this->priority = Priority::from($this->priority);
        }
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): ObjectiveDTO
    {
        // The constructor will handle converting string values to enum instances
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['negotiation_id'] ?? null,
            $data['created_by_id'] ?? null,
            $data['priority'] ?? 'low',
            $data['status'] ?? 'In Progress',
            $data['objective'] ?? null,
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
            'tenant_id' => $this->tenant_id,
            'negotiation_id' => $this->negotiation_id,
            'created_by_id' => $this->created_by_id,
            'priority' => $this->priority instanceof Priority ? $this->priority->value : $this->priority,
            'status' => $this->status,
            'objective' => $this->objective,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
