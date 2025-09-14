<?php

namespace App\Services\Log;

use App\Contracts\LogRepositoryInterface;
use App\DTOs\Log\CreateLogDTO;
use App\Events\Log\LogCreatedEvent;
use App\Jobs\Log\ProcessLogCreationJob;
use App\Models\Log;
use Illuminate\Database\Eloquent\Model;

class LogService
{
    public function __construct(private readonly LogRepositoryInterface $repo)
    {
    }

    /**
     * Write a log entry asynchronously using a queued job
     *
     * @param int $tenantId
     * @param string $event
     * @param string $headline
     * @param Model|null $about
     * @param Model|null $by
     * @param string|null $description
     * @param array $properties
     * @param string $channel
     * @param string $severity
     * @return void
     */
    public function writeAsync(
        int $tenantId,
        string $event,
        string $headline,
        ?Model $about = null,
        ?Model $by = null,
        ?string $description = null,
        array $properties = [],
        string $channel = 'app',
        string $severity = 'info',
    ): void {
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

        // Dispatch the job to the queue
        ProcessLogCreationJob::dispatch($data);

        // Optionally, dispatch an event for other listeners
        event(new LogCreatedEvent($data));
    }

    /**
     * Write a log entry synchronously (for backward compatibility)
     *
     * @param int $tenantId
     * @param string $event
     * @param string $headline
     * @param Model|null $about
     * @param Model|null $by
     * @param string|null $description
     * @param array $properties
     * @param string $channel
     * @param string $severity
     * @return Log
     */
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
