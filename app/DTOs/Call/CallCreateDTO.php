<?php

namespace App\DTOs\Call;

use Illuminate\Support\Arr;

final readonly class CallCreateDTO
{
    public function __construct(
        public int $tenantId,
        public ?int $negotiationId,
        public string $toE164,
        public string $fromE164,
        public string $direction, // 'outbound-api'
        public ?int $createdBy,
        public array $meta = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            tenantId: (int) $data['tenant_id'],
            negotiationId: $data['negotiation_id'] ?? null,
            toE164: $data['to'],
            fromE164: $data['from'] ?? config('twilio.from'),
            direction: 'outbound-api',
            createdBy: $data['created_by'] ?? null,
            meta: Arr::except($data, ['tenant_id', 'negotiation_id', 'to', 'from', 'created_by']),
        );
    }
}
