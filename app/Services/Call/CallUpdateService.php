<?php

namespace App\Services\Call;

use App\DTOs\Call\CallStatusCallbackDTO;
use App\DTOs\Call\RecordingCallbackDTO;
use App\Events\Call\CallUpdatedEvent;
use App\Repositories\Call\CallRecordingRepository;
use App\Repositories\Call\CallRepository;

class CallUpdateService
{
    public function __construct(
        private CallRepository $calls,
        private CallRecordingRepository $recordings
    ) {
    }

    public function handleStatus(CallStatusCallbackDTO $dto): void
    {
        $attrs = [
            'account_sid' => $dto->accountSid,
            'call_sid' => $dto->callSid,
            'parent_call_sid' => $dto->parentCallSid,
            'status' => $dto->status,
            'direction' => $dto->direction,
            'from_e164' => $dto->from,
            'to_e164' => $dto->to,
            'api_version' => $dto->apiVersion,
            'caller_name' => $dto->callerName,
            'forwarded_from' => $dto->forwardedFrom,
            'answered_by' => $dto->answeredBy,
            'sip_response_code' => $dto->sipResponseCode,
            'error_code' => $dto->errorCode,
            'last_event_type' => 'status',
            'last_event_payload' => $dto->raw,
            'last_event_at' => $dto->occurredAt,
        ];

        // set timeline markers
        $timeline = match ($dto->status) {
            'queued' => ['queued_at' => $dto->occurredAt],
            'ringing' => ['ringing_at' => $dto->occurredAt],
            'in-progress' => ['answered_at' => $dto->occurredAt],
            'completed' => ['completed_at' => $dto->occurredAt, 'duration_seconds' => $dto->callDuration],
            default => [],
        };

        $call = $this->calls->upsertFromStatusCallback(array_merge($attrs, $timeline));
        $this->calls->addEvent($call->id, 'status.'.$dto->status, $dto->raw, $dto->occurredAt);

        broadcast(new CallUpdatedEvent($call->fresh()))->toOthers();
    }

    public function handleRecording(RecordingCallbackDTO $dto): void
    {
        $call = $this->calls->findBySid($dto->callSid);
        if (! $call) {
            // Optionally create shell call here; usually it exists.
            return;
        }

        $rec = $this->recordings->upsert([
            'call_id' => $call->id,
            'recording_sid' => $dto->recordingSid,
            'status' => $dto->status,
            'duration_seconds' => $dto->durationSec,
            'channels' => $dto->channels,
            'source' => $dto->source,
            'recording_url' => $dto->url,
            'started_at' => $dto->occurredAt,
        ]);

        $this->calls->setHasRecordings($call->id, true);
        $this->calls->addEvent($call->id, 'recording.'.$dto->status, $dto->raw, $dto->occurredAt);

        // Kick off background download to S3
        dispatch(new \App\Jobs\DownloadTwilioRecordingJob($rec->id));

        broadcast(new CallUpdatedEvent($call->fresh()))->toOthers();
    }
}
