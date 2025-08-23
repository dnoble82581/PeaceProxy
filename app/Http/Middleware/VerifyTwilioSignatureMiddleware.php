<?php

namespace App\Http\Middleware;

use Closure;
use Twilio\Security\RequestValidator;

class VerifyTwilioSignatureMiddleware
{
    public function handle($request, Closure $next)
    {
        $signature = $request->header('X-Twilio-Signature');
        $validator = new RequestValidator(config('twilio.auth_token'));
        $url = $request->fullUrl(); // MUST be the exact URL Twilio called (https + query)

        if (! $validator->validate($signature, $url, $request->post())) {
            abort(403, 'Invalid Twilio signature');
        }

        return $next($request);
    }
}
