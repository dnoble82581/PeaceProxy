<?php

	/**
	 * FILE: resources/views/livewire/pricing.volt.php
	 * TYPE: Livewire Volt (anonymous class-based) component
	 * STACK: Laravel 12, Tailwind v4, Alpine.js, Stripe Elements
	 * PURPOSE: Pricing table (Monthly/Yearly) + Stripe card modal + subscription flow for TENANT-first billing
	 */

	use App\Services\Billing\StripeService;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Auth;
	use Stripe\Exception\ApiErrorException;

	new class extends Component {
		public string $clientSecret = '';
		public string $publishableKey = '';
		public string $priceMonthly = '';
		public string $priceYearly = '';

		public function mount():void
		{
			$tenant = Auth::user()->tenant;
			$intent = $tenant->createSetupIntent();

			$this->clientSecret = $intent->client_secret;
			$this->publishableKey = (string) config('services.stripe.key');
			$this->priceMonthly = (string) config('billing.prices.team_monthly');
			$this->priceYearly = (string) config('billing.prices.team_yearly');
		}

		// Livewire action invoked from JS after collecting a PM with Elements
		public function startMonthlySubscription(StripeService $stripeService):void
		{
			$session = $stripeService->startSubscription(config('billing.prices.team_monthly'));

			$this->redirect($session->url);
		}

		public function startYearlySubscription(StripeService $stripeService):void
		{
			$session = $stripeService->startSubscription(config('billing.prices.team_yearly'));

			$this->redirect($session->url);
		}

		// Optional: open Stripe Customer Portal
		public function managePortal()
		{
			$tenant = auth()->user()->tenant;
			$tenant->createOrGetStripeCustomer();

			try {
				// Ensure absolute return URL and tenant-aware domain if you use subdomains
				$returnUrl = route('billing.index', ['tenantSubdomain' => tenant()->subdomain], true);
				$url = $tenant->billingPortalUrl($returnUrl);
				return $this->redirect($url);
			} catch (ApiErrorException $e) {
				// This is what you’ll see when portal isn’t configured in the current mode
				report($e);
				session()->flash('error',
					'Customer Portal isn’t configured for this Stripe mode. In Stripe, go to Settings → Billing → Customer portal and click Save.');
				return $this->redirectRoute('dashboard', ['tenantSubdomain' => tenant()->subdomain]);
			}
		}
	};
?>

<div class="min-h-[calc(100vh-6rem)] bg-neutral-50 py-12 dark:bg-dark-800">
	<div class="mx-auto max-w-6xl px-4">
		<header class="text-center mb-10">
			<h1 class="text-3xl font-bold tracking-tight">Choose your plan</h1>
			<p class="mt-2 text-neutral-600 dark:text-dark-300">Tenant-first billing — one subscription per agency.</p>
		</header>

		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<!-- Monthly -->
			<div class="rounded-2xl border flex flex-col gap-4 justify-evenly border-neutral-200 bg-white dark:bg-dark-700 p-6 shadow-sm">
				<div class="flex items-baseline gap-2">
					<h2 class="text-xl font-semibold">Agency Plan</h2>
					<x-badge
							round
							color="black"
							xs
							text="Monthly" />
				</div>
				<div class="mt-4 flex items-end gap-1">
					<span class="text-4xl font-bold">$299</span>
					<span class="text-neutral-500">/mo</span>
				</div>
				<ul class="space-y-2 text-sm text-neutral-700 dark:text-neutral-200">
					<li>✔ Unlimited incidents</li>
					<li>✔ Team collaboration</li>
					<li>✔ Real-time chat (Reverb)</li>
					<li>✔ Reports & exports</li>
					<li>✔ Email support</li>
				</ul>
				<x-button
						loading="startMonthlySubscription"
						wire:click="startMonthlySubscription"
						class="w-full"
				>Start monthly
				</x-button>
			</div>

			<!-- Yearly -->
			<div class="rounded-2xl h-full border flex flex-col justify-between border-neutral-200 bg-white p-6 shadow-sm dark:bg-dark-700 dark:text-dark-300">
				<div class="flex items-baseline gap-2">
					<h2 class="text-xl font-semibold">Agency Plan</h2>
					<x-badge
							round
							text="Recommended"
							color="primary"
							class="animate-pulse" />
				</div>
				<div class="mt-4 flex items-end gap-1">
					<span class="text-4xl font-bold">$2999</span>
					<span class="text-neutral-500">/yr</span>
				</div>
				<ul class="mt-6 space-y-2 text-sm text-neutral-700 dark:text-dark-300">
					<li>✔ 2 months free compared to monthly</li>
					<li>✔ All monthly features</li>
					<li>✔ Priority support</li>
				</ul>
				<div class="">
					<x-button
							loading="startYearlySubscription"
							class="w-full"
							wire:click="startYearlySubscription"
					>Start yearly
					</x-button>
				</div>

			</div>
		</div>

		<div class="mt-8 text-center">
			<x-button
					loading="managePortal"
					wire:click="managePortal"
			>Manage billing
			</x-button>
		</div>

		@if (session('ok'))
			<div class="mt-6 rounded-xl bg-emerald-50 text-emerald-900 px-4 py-3 text-sm">
				{{ session('ok') }}
			</div>
		@endif
	</div>
</div>


@once
	<script src="https://js.stripe.com/v3/"></script>
@endonce
