<?php

namespace App\Services\Log;

use App\DTOs\Log\CreateLogDTO;
use App\Models\Log;
use Illuminate\Database\Eloquent\Model;

class LogService
{
    public function __construct(private readonly \App\Repositories\Log\LogRepository $repo)
    {
    }

    public function write(
        int $tenantId,
        string $event,
        string $headline,
        ?Model $about = null,
        ?Model $by = null,
        ?string $description = null,
        array $properties = [],
        string $channel = 'app',
        string $severity = 'info',
    ): Log {
        $data = new CreateLogDTO(
            tenantId: $tenantId,
            loggableType: $about?->getMorphClass(),
            loggableId: $about?->getKey(),
            actorType: $by?->getMorphClass(),
            actorId: $by?->getKey(),
            event: $event,
            headline: $headline,
            description: $description,
            properties: $properties,
            channel: $channel,
            severity: $severity,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
            occurredAt: now(),
        );

        return $this->repo->create($data);
    }
}
