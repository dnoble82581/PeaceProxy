<?php

namespace App\DTOs\Call;

final readonly class RecordingCallbackDTO
{
    public function __construct(
        public string $callSid,
        public string $recordingSid,
        public ?string $status,         // queued|in-progress|completed|failed
        public ?int $durationSec,
        public ?string $channels,       // mono|dual
        public ?string $source,         // dial|trunking|...
        public ?string $url,
        public \DateTimeImmutable $occurredAt,
        public array $raw
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $r): self
    {
        return new self(
            callSid: (string) $r->input('CallSid'),
            recordingSid: (string) $r->input('RecordingSid'),
            status: $r->input('RecordingStatus'),
            durationSec: $r->filled('RecordingDuration') ? (int) $r->input('RecordingDuration') : null,
            channels: $r->input('RecordingChannels'),
            source: $r->input('RecordingSource'),
            url: $r->input('RecordingUrl'),
            occurredAt: new \DateTimeImmutable('now'),
            raw: $r->all()
        );
    }
}
