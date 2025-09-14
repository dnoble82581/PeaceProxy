<?php

namespace App\Listeners\Log;

use App\Events\Log\LogCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param LogCreatedEvent $event
     * @return void
     */
    public function handle(LogCreatedEvent $event): void
    {
        // Example of additional processing that could be done when a log is created
        // This could include sending notifications, updating dashboards, etc.

        // For now, just log that we received the event
        Log::info('LogCreatedEvent received', [
            'tenant_id' => $event->logData->tenantId,
            'event' => $event->logData->event,
            'headline' => $event->logData->headline,
        ]);

        // Additional processing could be added here
        // For example:
        // - Send notifications for critical logs
        // - Update real-time dashboards
        // - Sync with external monitoring systems
    }
}
