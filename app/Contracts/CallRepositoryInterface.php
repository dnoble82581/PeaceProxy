<?php

namespace App\Contracts;

use App\DTOs\Call\CallCreateDTO;
use App\Models\Call;

interface CallRepositoryInterface
{
    public function createForOutbound(CallCreateDTO $dto, array $twilio): Call; // returns persisted Call
    public function findBySid(string $callSid): ?Call;
    public function upsertFromStatusCallback(array $attrs): Call; // idempotent by call_sid
    public function addEvent(int $callId, string $type, array $payload, \DateTimeInterface $at): void;
    public function markCompleted(string $callSid, ?int $durationSec): void;
    public function setHasRecordings(int $callId, bool $has = true): void;
}
