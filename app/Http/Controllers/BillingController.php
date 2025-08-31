<?php

namespace App\Http\Controllers;

use Request;

class BillingController
{
    // app/Http/Controllers/BillingController.php
    public function showSubscribe(Request $request)
    {
        $tenant = $request->user()->tenant;
        $tenant->createOrGetStripeCustomer();        // ensures stripe_id

        $intent = $tenant->createSetupIntent();

        return view('billing.subscribe', [
            'clientSecret' => $intent->client_secret,
            'publishableKey' => config('services.stripe.key'),
            'priceId' => config('billing.prices.team_monthly'),
        ]);
    }

    public function startSubscription(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
            'price_id' => 'required',
        ]);

        $tenant = $request->user()->tenant;

        $tenant->updateDefaultPaymentMethod($request->payment_method);

        $tenant->newSubscription('default', $request->price_id)
            ->trialDays(14)
            // ->quantity($tenant->users()->count()) // per-seat (optional)
            ->create($request->payment_method);

        return to_route('billing.index')->with('ok', 'Subscribed!');
    }
}
