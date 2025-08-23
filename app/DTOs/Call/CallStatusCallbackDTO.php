<?php

namespace App\DTOs\Call;

final readonly class CallStatusCallbackDTO
{
    public function __construct(
        public string $accountSid,
        public string $callSid,
        public ?string $parentCallSid,
        public string $status,          // initiated|ringing|in-progress|completed|busy|failed|no-answer|canceled
        public string $direction,       // inbound|outbound-api|outbound-dial
        public string $from,
        public string $to,
        public ?string $apiVersion,
        public ?string $callerName,
        public ?string $forwardedFrom,
        public ?string $answeredBy,     // human|machine|machine_start|...
        public ?int $callDuration,   // seconds, only on completed
        public ?int $sipResponseCode,
        public ?int $errorCode,
        public \DateTimeImmutable $occurredAt,
        public array $raw
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $r): self
    {
        return new self(
            accountSid: (string) $r->input('AccountSid'),
            callSid: (string) $r->input('CallSid'),
            parentCallSid: $r->input('ParentCallSid'),
            status: (string) $r->input('CallStatus'),
            direction: (string) $r->input('Direction'),
            from: (string) $r->input('From'),
            to: (string) $r->input('To'),
            apiVersion: $r->input('ApiVersion'),
            callerName: $r->input('CallerName'),
            forwardedFrom: $r->input('ForwardedFrom'),
            answeredBy: $r->input('AnsweredBy'),
            callDuration: $r->filled('CallDuration') ? (int) $r->input('CallDuration') : null,
            sipResponseCode: $r->filled('SipResponseCode') ? (int) $r->input('SipResponseCode') : null,
            errorCode: $r->filled('ErrorCode') ? (int) $r->input('ErrorCode') : null,
            occurredAt: new \DateTimeImmutable('now'),
            raw: $r->all()
        );
    }
}
