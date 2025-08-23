<?php

namespace App\Services\Call;

use App\DTOs\Call\CallStatusCallbackDTO;
use App\DTOs\Call\RecordingCallbackDTO;

class WebhookDispatcher
{
    public function __construct(private CallUpdateService $updates)
    {
    }

    public function dispatchStatus(\Illuminate\Http\Request $r): \Symfony\Component\HttpFoundation\Response
    {
        $dto = CallStatusCallbackDTO::fromRequest($r);
        $this->updates->handleStatus($dto);

        return response('ok');
    }

    public function dispatchRecording(\Illuminate\Http\Request $r): \Symfony\Component\HttpFoundation\Response
    {
        $dto = RecordingCallbackDTO::fromRequest($r);
        $this->updates->handleRecording($dto);

        return response('ok');
    }

    public function dispatchGather(\Illuminate\Http\Request $r): \Symfony\Component\HttpFoundation\Response
    {
        // optional: capture Digits, add event, maybe change TwiML next step
        return response('<?xml version="1.0" encoding="UTF-8"?><Response></Response>', 200, ['Content-Type' => 'text/xml']);
    }
}
