<?php

namespace App\Events\Log;

use App\DTOs\Log\CreateLogDTO;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogCreatedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param CreateLogDTO $logData The data for creating a log entry
     */
    public function __construct(
        public readonly CreateLogDTO $logData
    ) {
    }
}
