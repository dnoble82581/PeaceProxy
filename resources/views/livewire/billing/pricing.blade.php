<?php

	/**
	 * FILE: resources/views/livewire/pricing.volt.php
	 * TYPE: Livewire Volt (anonymous class-based) component
	 * STACK: Laravel 12, Tailwind v4, Alpine.js, Stripe Elements
	 * PURPOSE: Pricing table (Monthly/Yearly) + Stripe card modal + subscription flow for TENANT-first billing
	 */

	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Auth;

	new class extends Component {
		public string $clientSecret = '';
		public string $publishableKey = '';
		public string $priceMonthly = '';
		public string $priceYearly = '';

		public function mount():void
		{
			$tenant = Auth::user()->tenant;
			$tenant->createOrGetStripeCustomer();
			$intent = $tenant->createSetupIntent();

			$this->clientSecret = $intent->client_secret;
			$this->publishableKey = (string) config('services.stripe.key');
			$this->priceMonthly = (string) config('billing.prices.team_monthly');
			$this->priceYearly = (string) config('billing.prices.team_yearly');
		}

		// Livewire action invoked from JS after collecting a PM with Elements
		public function startSubscription(string $priceId, string $paymentMethodId):void
		{
			$tenant = Auth::user()->tenant;

			$tenant->updateDefaultPaymentMethod($paymentMethodId);
			$subscription = $tenant->newSubscription('default', $priceId)
				->trialDays(30) // Add a 30-day free trial
				->create($paymentMethodId);

			// Save the trial end date to the tenant model
			$tenant->trial_ends_at = $subscription->trial_ends_at;
			$tenant->save();

			session()->flash('ok', 'Subscribed successfully. Your 30-day free trial has started.');
			$this->dispatch('userSubscribed');
			// define this route to your billing dashboard
		}

		// Optional: open Stripe Customer Portal
		public function managePortal()
		{
			$tenant = auth()->user()->tenant;
			$tenant->createOrGetStripeCustomer();

			try {
				// Ensure absolute return URL and tenant-aware domain if you use subdomains
				$returnUrl = route('billing.index', ['tenantSubdomain' => tenant()->subdomain], true);
				return $tenant->redirectToBillingPortal($returnUrl);
			} catch (\Stripe\Exception\ApiErrorException $e) {
				// This is what you’ll see when portal isn’t configured in the current mode
				report($e);
				session()->flash('error',
					'Customer Portal isn’t configured for this Stripe mode. In Stripe, go to Settings → Billing → Customer portal and click Save.');
				return $this->redirectRoute('dashboard', ['tenantSubdomain' => tenant()->subdomain]);
			}
		}
	};
?>

<div
		x-data="{
        modal:false,
        selectedPrice:null,
        processing:false,
        async open(price){
            this.selectedPrice = price;
            this.modal = true;
            await this.$nextTick();
            // Initialize Stripe Elements only once per open
            if(!window.__ppStripe){
                window.__ppStripe = Stripe(@js($publishableKey));
                const elements = window.__ppStripe.elements({clientSecret: @js($clientSecret)});
                const paymentElement = elements.create('payment');
                paymentElement.mount('#pp-payment-element');
                window.__ppElements = elements;
            }
        },
        async subscribe(){
            if(!window.__ppStripe || !window.__ppElements) return;
            this.processing = true;
            const {error, setupIntent} = await window.__ppStripe.confirmSetup({
                elements: window.__ppElements,
                redirect: 'if_required',
            });
            if(error){
                this.processing = false;
                this.$dispatch('toast', {type:'error', text: error.message});
                return;
            }
            // Pass PM to Livewire action
            $wire.startSubscription(this.selectedPrice, setupIntent.payment_method);
        }
    }"
		class="min-h-[calc(100vh-6rem)] bg-neutral-50 py-12 dark:bg-dark-800"
>
	<div class="mx-auto max-w-6xl px-4">
		<header class="text-center mb-10">
			<h1 class="text-3xl font-bold tracking-tight">Choose your plan</h1>
			<p class="mt-2 text-neutral-600 dark:text-dark-300">Tenant-first billing — one subscription per agency.</p>
		</header>

		<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
			<!-- Free Trial -->
			<div class="rounded-2xl border border-neutral-200 bg-white dark:bg-dark-700 p-6 shadow-sm">
				<div class="flex items-baseline gap-2">
					<h2 class="text-xl font-semibold">Free Trial</h2>
					<x-badge
							text="Free"
							color="slate"
							round
							xs />
				</div>
				<div class="mt-4 flex items-end gap-1">
					<span class="text-4xl font-bold">$0</span>
					<span class="text-neutral-500">/mo</span>
				</div>
				<ul class="mt-6 space-y-2 text-sm text-neutral-700 dark:text-neutral-200">
					<li>✔ Full access for 30 days</li>
					<li>✔ No credit card required</li>
					<li>✔ Cancel anytime</li>
					<li>✔ All features included</li>
					<li>✔ Email support</li>
				</ul>
				<button
						@click="open(@js($priceMonthly))"
						class="mt-6 w-full rounded-xl bg-slate-600 text-white py-3 font-medium hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-600/20"
				>Start free trial
				</button>
			</div>

			<!-- Monthly -->
			<div class="rounded-2xl border border-neutral-200 bg-white dark:bg-dark-700 p-6 shadow-sm">
				<div class="flex items-baseline gap-2">
					<h2 class="text-xl font-semibold">Agency Plan</h2>
					<x-badge
							round
							color="black"
							xs
							text="Monthly" />
				</div>
				<div class="mt-4 flex items-end gap-1">
					<span class="text-4xl font-bold">$99</span>
					<span class="text-neutral-500">/mo</span>
				</div>
				<ul class="mt-6 space-y-2 text-sm text-neutral-700 dark:text-neutral-200">
					<li>✔ Unlimited incidents</li>
					<li>✔ Team collaboration</li>
					<li>✔ Real-time chat (Reverb)</li>
					<li>✔ Reports & exports</li>
					<li>✔ Email support</li>
				</ul>
				<button
						@click="open(@js($priceMonthly))"
						class="mt-6 w-full rounded-xl bg-black text-white py-3 font-medium hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-black/20"
				>Start monthly
				</button>
			</div>

			<!-- Yearly -->
			<div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:bg-dark-700 dark:text-dark-300">
				<div class="flex items-baseline gap-2">
					<h2 class="text-xl font-semibold">Agency Plan</h2>
					<x-badge
							round
							text="Recommended"
							color="primary"
							class="animate-pulse" />
				</div>
				<div class="mt-4 flex items-end gap-1">
					<span class="text-4xl font-bold">$999</span>
					<span class="text-neutral-500">/yr</span>
				</div>
				<ul class="mt-6 space-y-2 text-sm text-neutral-700 dark:text-dark-300">
					<li>✔ 2 months free compared to monthly</li>
					<li>✔ All monthly features</li>
					<li>✔ Priority support</li>
				</ul>
				<button
						@click="open(@js($priceYearly))"
						class="mt-6 w-full rounded-xl bg-primary-600 text-white py-3 font-medium hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-black/20"
				>Start yearly
				</button>
			</div>
		</div>

		<div class="mt-8 text-center">
			<button
					wire:click="managePortal"
					class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-4 py-2 text-sm font-medium dark:text-dark-800 hover:bg-neutral-50"
			>Manage billing
			</button>
		</div>

		@if (session('ok'))
			<div class="mt-6 rounded-xl bg-emerald-50 text-emerald-900 px-4 py-3 text-sm">
				{{ session('ok') }}
			</div>
		@endif
	</div>

	<!-- Modal -->
	<div
			x-show="modal"
			x-transition.opacity
			class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
			style="display: none;"
			@keydown.escape.window="modal=false"
	>
		<div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
			<div class="flex items-center justify-between">
				<h3 class="text-lg font-semibold">Add payment method</h3>
				<button
						@click="modal=false"
						class="p-1 rounded-lg hover:bg-neutral-100 dark:text-dark-700">✕
				</button>
			</div>
			<div
					class="mt-4"
					id="pp-payment-element"></div>
			<button
					@click="subscribe()"
					:disabled="processing"
					class="mt-6 w-full rounded-xl bg-black text-white py-3 font-medium disabled:opacity-60"
			>
				<span x-show="!processing">Confirm & Subscribe</span>
				<span x-show="processing">Processing…</span>
			</button>
			<p class="mt-3 text-xs text-neutral-500">Secure payments by Stripe.</p>
		</div>
	</div>
</div>


@once
	<script src="https://js.stripe.com/v3/"></script>
@endonce
