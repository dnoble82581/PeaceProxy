<?php

namespace App\Http\Controllers\Twilio;

use Illuminate\Http\Response;
use Twilio\TwiML\VoiceResponse;

class TwiMLController
{
    public function initial(): Response
    {
        // If you use Debugbar locally, disable it for this XML response
        //        if (app()->bound('debugbar')) {
        //            app('debugbar')->disable();
        //        }

        $twiml = new VoiceResponse();
        $twiml->say('Connecting you now.', ['voice' => 'Polly.Joanna']);

        $dial = $twiml->dial(null, [
            'answerOnBridge' => true,
            'recordingStatusCallback' => route('twilio.recording', tenant()->subdomain),
            'recordingStatusCallbackEvent' => 'completed',
        ]);
        $dial->number('+13195947290');

        return response((string) $twiml, 200)
            ->header('Content-Type', 'text/xml');
    }
}
