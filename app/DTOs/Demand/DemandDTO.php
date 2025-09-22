<?php

namespace App\DTOs\Demand;

use App\Enums\Demand\DemandCategories;
use App\Enums\Demand\DemandStatuses;
use App\Enums\General\Channels;
use App\Enums\General\RiskLevels;
use Carbon\Carbon;

class DemandDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?int $subject_id = null,
        public ?int $negotiation_id = null,
        public ?int $created_by_id = null,
        public ?string $updated_by = null,
        public ?string $title = null,
        public ?string $content = null,
        public ?DemandCategories $category = null,
        public ?DemandStatuses $status = null,
        public ?RiskLevels $priority_level = null,
        public ?Channels $channel = null,
        public ?Carbon $deadline_date = null,
        public ?string $deadline_time = null,
        public ?Carbon $communicated_at = null,
        public ?Carbon $responded_at = null,
        public ?Carbon $resolved_at = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null,
    ) {
    }

    public static function fromArray(array $data): DemandDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['subject_id'] ?? null,
            $data['negotiation_id'] ?? null,
            $data['created_by_id'] ?? null,
            $data['updated_by'] ?? null,
            $data['title'] ?? null,
            $data['content'] ?? null,
            isset($data['category'])
                ? ($data['category'] instanceof DemandCategories
                    ? $data['category']
                    : DemandCategories::from($data['category']))
                : null,
            isset($data['status'])
                ? ($data['status'] instanceof DemandStatuses
                    ? $data['status']
                    : DemandStatuses::from($data['status']))
                : null,
            isset($data['priority_level'])
                ? ($data['priority_level'] instanceof RiskLevels
                    ? $data['priority_level']
                    : RiskLevels::from($data['priority_level']))
                : null,
            isset($data['channel'])
                ? ($data['channel'] instanceof Channels
                    ? $data['channel']
                    : Channels::from($data['channel']))
                : null,
            isset($data['deadline_date']) ? Carbon::parse($data['deadline_date']) : null,
            $data['deadline_time'] ?? null,
            isset($data['communicated_at']) ? Carbon::parse($data['communicated_at']) : null,
            isset($data['responded_at']) ? Carbon::parse($data['responded_at']) : null,
            isset($data['resolved_at']) ? Carbon::parse($data['resolved_at']) : null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            isset($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'subject_id' => $this->subject_id,
            'negotiation_id' => $this->negotiation_id,
            'created_by_id' => $this->created_by_id,
            'updated_by' => $this->updated_by,
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category?->value,
            'status' => $this->status?->value,
            'priority_level' => $this->priority_level?->value,
            'channel' => $this->channel?->value,
            'deadline_date' => $this->deadline_date,
            'deadline_time' => $this->deadline_time,
            'communicated_at' => $this->communicated_at,
            'responded_at' => $this->responded_at,
            'resolved_at' => $this->resolved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
