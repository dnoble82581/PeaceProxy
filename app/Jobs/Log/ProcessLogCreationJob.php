<?php

namespace App\Jobs\Log;

use App\Contracts\LogRepositoryInterface;
use App\DTOs\Log\CreateLogDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLogCreationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  CreateLogDTO  $logData  The data for creating a log entry
     */
    public function __construct(
        private readonly CreateLogDTO $logData
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(LogRepositoryInterface $repository): void
    {
        // Create the log entry using the repository
        $repository->create($this->logData);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'log',
            'tenant:'.$this->logData->tenantId,
            'event:'.$this->logData->event,
        ];
    }
}
