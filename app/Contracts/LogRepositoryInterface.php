<?php

namespace App\Contracts;

use App\DTOs\Log\CreateLogDTO;
use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;

interface LogRepositoryInterface
{
    /**
     * Create a new log entry
     *
     * @param CreateLogDTO $data
     * @return Log
     */
    public function create(CreateLogDTO $data): Log;

    /**
     * Get a log entry by ID
     *
     * @param int $id
     * @return Log|null
     */
    public function getLog(int $id): ?Log;

    /**
     * Get all logs for a tenant
     *
     * @param int $tenantId
     * @return Collection
     */
    public function getLogsByTenant(int $tenantId): Collection;

    /**
     * Get logs for a specific model
     *
     * @param string $loggableType
     * @param int $loggableId
     * @return Collection
     */
    public function getLogsForModel(string $loggableType, int $loggableId): Collection;

    /**
     * Get logs by event type
     *
     * @param string $event
     * @return Collection
     */
    public function getLogsByEvent(string $event): Collection;
}
