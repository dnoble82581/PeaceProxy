<?php

namespace App\DTOs\Log;

class CreateLogDTO
{
    public function __construct(
        public int $tenantId,
        public ?string $loggableType,
        public ?int $loggableId,
        public ?string $actorType,
        public ?int $actorId,
        public string $event,
        public string $headline,
        public ?string $description = null,
        public array $properties = [],
        public string $channel = 'app',
        public string $severity = 'info',
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
        public ?\DateTimeInterface $occurredAt = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'loggable_type' => $this->loggableType,
            'loggable_id' => $this->loggableId,
            'actor_type' => $this->actorType,
            'actor_id' => $this->actorId,
            'event' => $this->event,
            'headline' => $this->headline,
            'description' => $this->description,
            'properties' => $this->properties,
            'channel' => $this->channel,
            'severity' => $this->severity,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'occurred_at' => ($this->occurredAt ?? now()),
        ];
    }
}
