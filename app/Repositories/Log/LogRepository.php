<?php

namespace App\Repositories\Log;

use App\Contracts\LogRepositoryInterface;
use App\DTOs\Log\CreateLogDTO;
use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;

class LogRepository implements LogRepositoryInterface
{
    /**
     * Create a new log entry
     *
     * @param CreateLogDTO $data
     * @return Log
     */
    public function create(CreateLogDTO $data): Log
    {
        return Log::create($data->toArray());
    }

    /**
     * Get a log entry by ID
     *
     * @param int $id
     * @return Log|null
     */
    public function getLog(int $id): ?Log
    {
        return Log::find($id);
    }

    /**
     * Get all logs for a tenant
     *
     * @param int $tenantId
     * @return Collection
     */
    public function getLogsByTenant(int $tenantId): Collection
    {
        return Log::forTenant($tenantId)->orderBy('occurred_at', 'desc')->get();
    }

    /**
     * Get logs for a specific model
     *
     * @param string $loggableType
     * @param int $loggableId
     * @return Collection
     */
    public function getLogsForModel(string $loggableType, int $loggableId): Collection
    {
        return Log::where('loggable_type', $loggableType)
            ->where('loggable_id', $loggableId)
            ->orderBy('occurred_at', 'desc')
            ->get();
    }

    /**
     * Get logs by event type
     *
     * @param string $event
     * @return Collection
     */
    public function getLogsByEvent(string $event): Collection
    {
        return Log::event($event)->orderBy('occurred_at', 'desc')->get();
    }
}
