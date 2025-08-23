<?php

namespace App\Http\Controllers\Twilio;

use App\Services\Call\WebhookDispatcher;
use Illuminate\Http\Request;

class WebhookController
{
    public function __construct(private WebhookDispatcher $dispatcher)
    {
    }

    public function status(Request $r)
    {
        return $this->dispatcher->dispatchStatus($r);
    }

    public function recording(Request $r)
    {
        return $this->dispatcher->dispatchRecording($r);
    }

    public function gather(Request $r)
    {
        return $this->dispatcher->dispatchGather($r);
    }
}
