<?php

namespace App\Listeners\Log;

use App\Services\Log\LogService;

class WriteActivityLogListener
{
    public function __construct(private LogService $logs)
    {
    }

    public function handle($event): void
    {
        // Each event exposes tenantId, actor, subject, summary, etc.
        $this->logs->write(
            tenantId: $event->tenantId,
            event: $event->eventKey,         // e.g. 'message.sent'
            headline: $event->headline,         // "Sgt. Hall sent a message"
            about: $event->subjectModel ?? null,
            by: $event->actorModel ?? null,
            description: $event->description ?? null,
            properties: $event->properties ?? [],
            channel: $event->channel ?? 'app',
            severity: $event->severity ?? 'info',
        );
    }
}
