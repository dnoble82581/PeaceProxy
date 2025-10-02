<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Stripe;

class DonationController
{
    /**
     * Create a Stripe Checkout session for a one-time donation and redirect.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:100000'], // amount in dollars
        ]);

        $amountDollars = (float) $validated['amount'];
        $amountCents = (int) round($amountDollars * 100);

        // Initialize Stripe
        $secret = config('services.stripe.secret') ?? config('cashier.secret');
        if (! $secret) {
            return back()->with('error', 'Stripe is not configured.');
        }
        Stripe::setApiKey($secret);

        $successUrl = URL::route('donations.success');
        $cancelUrl = URL::route('donations.cancel');

        $customerEmail = Auth::check() ? (string) optional(Auth::user())->email : null;

        $session = StripeCheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => config('cashier.currency', 'usd'),
                    'unit_amount' => $amountCents,
                    'product_data' => [
                        'name' => 'Peace Proxy – Donation',
                        'description' => 'Support ongoing development and operations',
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl.'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'customer_email' => $customerEmail,
            'metadata' => [
                'source' => 'welcome_page_donation',
            ],
            // Set locale automatically
            'locale' => 'auto',
        ]);

        return redirect()->away($session->url);
    }

    /**
     * Handle a successful donation redirect from Stripe Checkout.
     */
    public function success(Request $request): RedirectResponse
    {
        return redirect()->to(url('/') . '#support-the-project')
            ->with('ok', 'Thank you! Your donation helps keep Peace Proxy moving forward.');
    }

    /**
     * Handle a cancelled donation redirect from Stripe Checkout.
     */
    public function cancel(Request $request): RedirectResponse
    {
        return redirect()->to(url('/') . '#support-the-project')
            ->with('info', 'Donation cancelled. No charges were made.');
    }
}
