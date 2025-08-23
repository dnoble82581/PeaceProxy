<?php

namespace App\Services\Call;

use App\Contracts\CallRepositoryInterface;
use App\DTOs\Call\CallCreateDTO;
use Twilio\Rest\Client as Twilio;

class CallPlacementService
{
    public function __construct(
        private Twilio $twilio,
        private CallRepositoryInterface $calls
    ) {
    }

    public function dialSubject(CallCreateDTO $dto, ?string $tenantSlugOrId, array $twimlParams = []): \App\Models\Call
    {
        // Build callback endpoints containing a tenant hint (slug or ID)
        $statusUrl = route('twilio.status', $tenantSlugOrId);
        $twimlUrl = route('twilio.twiml.initial', $tenantSlugOrId);

        $resp = $this->twilio->calls->create(
            $dto->toE164,
            $dto->fromE164,
            [
                'url' => $twimlUrl,
                'statusCallback' => $statusUrl,
                'statusCallbackEvent' => ['initiated', 'ringing', 'answered', 'completed'],
                'statusCallbackMethod' => 'POST',
                // Examples:
                // 'record' => 'record-from-answer-dual',
                // 'machineDetection' => 'DetectMessageEnd'
            ] + $twimlParams
        );

        return $this->calls->createForOutbound(
            $dto,
            [
                'account_sid' => $resp->accountSid,
                'call_sid' => $resp->sid,
                'parent_call_sid' => $resp->parentCallSid ?? null,
                // optionally pass callable linkage if you know it here
            ]
        );
    }
}
