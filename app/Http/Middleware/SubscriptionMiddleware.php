<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if (! tenant()->hasActiveSubscription()) {
            return redirect()->route('tenant.pricing');
        }
        return $next($request);
    }
}
