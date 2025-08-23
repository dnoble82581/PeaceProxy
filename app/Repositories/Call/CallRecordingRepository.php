<?php

namespace App\Repositories\Call;

use App\DTOs\Call\CallCreateDTO;
use App\Models\Call;
use Illuminate\Support\Facades\DB;

class CallRecordingRepository
{
    public function createForOutbound(CallCreateDTO $dto, array $twilio): Call
    {
        return DB::transaction(function () use ($dto, $twilio) {
            /** @var Call $call */
            $call = Call::query()->create([
                'tenant_id' => $dto->tenantId,
                'negotiation_id' => $dto->negotiationId,
                'callable_type' => $twilio['callable_type'] ?? null,
                'callable_id' => $twilio['callable_id'] ?? null,
                'created_by' => $dto->createdBy,
                'account_sid' => $twilio['account_sid'],
                'call_sid' => $twilio['call_sid'],
                'parent_call_sid' => $twilio['parent_call_sid'] ?? null,
                'direction' => 'outbound-api',
                'from_e164' => $dto->fromE164,
                'to_e164' => $dto->toE164,
                'status' => 'queued',
                'queued_at' => now(),
                'meta' => $dto->meta,
            ]);

            return $call;
        });
    }

    public function findBySid(string $callSid): ?Call
    {
        return Call::query()->where('call_sid', $callSid)->first();
    }

    /**
     * @throws \Throwable
     */
    public function upsert(array $attrs): Call
    {
        return DB::transaction(function () use ($attrs) {
            $call = Call::query()->firstOrNew(['call_sid' => $attrs['call_sid']]);
            $call->fill($attrs);
            $call->save();

            return $call;
        });
    }

    public function addEvent(int $callId, string $type, array $payload, \DateTimeInterface $at): void
    {
        \App\Models\CallEvent::query()->create([
            'call_id' => $callId,
            'type' => $type,
            'payload' => $payload,
            'occurred_at' => $at,
        ]);
    }

    public function markCompleted(string $callSid, ?int $durationSec): void
    {
        Call::query()->where('call_sid', $callSid)->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration_seconds' => $durationSec,
        ]);
    }

    public function setHasRecordings(int $callId, bool $has = true): void
    {
        Call::query()->whereKey($callId)->update(['has_recordings' => $has]);
    }
}
