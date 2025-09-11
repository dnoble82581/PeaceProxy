<?php
	/**
	 * FILE: resources/views/livewire/billing-index.volt.php
	 * TYPE: Livewire Volt (anonymous class-based) component
	 * STACK: Laravel 12, Tailwind v4, Alpine.js
	 * PURPOSE: Tenant-first billing dashboard (view subscription, manage plan, invoices, payment method)
	 */

	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Auth;
	use Stripe\Price;

	new class extends Component {
		public ?array $subscription = null;   // short array form for blade ease
		public array $invoices = [];
		public ?array $pm = null;             // default payment method
		public string $portalUrl = '';

		public function mount():void
		{
			$tenant = Auth::user()->tenant;

			// Ensure a Stripe customer exists so we can fetch PM/Invoices even if not subscribed yet
			$tenant->createOrGetStripeCustomer();

			$sub = $tenant->subscription('default');
			if ($sub) {
				$this->subscription = [
					'id' => $sub->id,
					'name' => $sub->name,
					'stripe_status' => $sub->stripe_status,
					'stripe_price' => $sub->stripe_price,
					'quantity' => $sub->quantity,
					'on_grace_period' => $sub->onGracePeriod(),
					'ends_at' => optional($sub->ends_at)?->toDateTimeString(),
					'trial_ends_at' => optional($sub->trial_ends_at)?->toDateTimeString(),
				];

				try {
					\Stripe\Stripe::setApiKey(config('services.stripe.secret'));
					$price = Price::retrieve($sub->stripe_price);

					$this->subscription['price_display'] =
						'$'.number_format($price->unit_amount / 100, 2).
						' / '.$price->recurring->interval;
				} catch (\Exception $e) {
					$this->subscription['price_display'] = 'Unknown';
				}
			}

			// Default payment method (if present)
			if ($pm = $tenant->defaultPaymentMethod()) {
				$this->pm = [
					'brand' => $pm->card?->brand,
					'last4' => $pm->card?->last4,
					'exp_month' => $pm->card?->exp_month,
					'exp_year' => $pm->card?->exp_year,
				];
			}

			// Recent invoices (limit 12)
			$this->invoices = collect($tenant->invoices())->take(12)->map(function ($inv) {
				$amountCents = (int) ($inv->total() ?? 0); // normalize to int
				return [
					'id' => $inv->id,
					'number' => $inv->number,
					'amount_cents' => $amountCents,
					'amount_display' => number_format($amountCents / 100, 2), // "12.34"
					'currency' => strtoupper($inv->currency),
					'date' => $inv->date()->toDateTimeString(),
					'status' => $inv->status,
					'hosted_invoice_url' => $inv->hosted_invoice_url,
					'invoice_pdf' => $inv->invoice_pdf,
				];
			})->all();
		}

		// Stripe Customer Portal (hosted)
		public function portal()
		{
			$tenant = auth()->user()->tenant;
			$tenant->createOrGetStripeCustomer();

			$session = $tenant->billingPortalUrl(route('dashboard', ['tenantSubdomain' => tenant()->subdomain]));
			return $this->redirect($session);
		}

		// Cancel at period end
		public function cancel()
		{
			$tenant = Auth::user()->tenant;
			$tenant->subscription('default')?->cancel();
			session()->flash('ok', 'Subscription will cancel at period end.');
			return $this->redirectRoute('billing.index', ['tenantSubdomain' => tenant()->subdomain]);
		}

		// Resume if on grace period
		public function resume()
		{
			$tenant = Auth::user()->tenant;
			$tenant->subscription('default')?->resume();
			session()->flash('ok', 'Subscription resumed.');
			return $this->redirectRoute('billing.index', ['tenantSubdomain' => tenant()->subdomain]);
		}

		// Swap plan (expects a price id from config/billing.php)
		public function swap(string $price)
		{
			$tenant = Auth::user()->tenant;
			$tenant->subscription('default')?->swap($price);
			session()->flash('ok', 'Plan updated.');

			// Refresh subscription data after swap to update UI without page reload
			$this->refreshSubscription();
//			return $this->redirectRoute('billing.index', ['tenantSubdomain' => tenant()->subdomain]);
		}

		// Refresh subscription data
		public function refreshSubscription()
		{
			$tenant = Auth::user()->tenant;
			$sub = $tenant->subscription('default');

			if ($sub) {
				$this->subscription = [
					'id' => $sub->id,
					'name' => $sub->name,
					'stripe_status' => $sub->stripe_status,
					'stripe_price' => $sub->stripe_price,
					'quantity' => $sub->quantity,
					'on_grace_period' => $sub->onGracePeriod(),
					'ends_at' => optional($sub->ends_at)?->toDateTimeString(),
					'trial_ends_at' => optional($sub->trial_ends_at)?->toDateTimeString(),
				];

				try {
					\Stripe\Stripe::setApiKey(config('services.stripe.secret'));
					$price = Price::retrieve($sub->stripe_price);

					$this->subscription['price_display'] =
						'$'.number_format($price->unit_amount / 100, 2).
						' / '.$price->recurring->interval;
				} catch (\Exception $e) {
					$this->subscription['price_display'] = 'Unknown';
				}
			}
		}

		// Optional: Update seat quantity
//		public function syncSeats(int $qty)
//		{
//			$tenant = Auth::user()->tenant;
//			$sub = $tenant->subscription('default');
//			if ($sub) {
//				$sub->updateQuantity(max(1, $qty));
//				session()->flash('ok', 'Seats updated.');
//			}
//			return $this->redirectRoute('billing.index', ['tenantSubdomain' => tenant()->subdomain]);
//		}
	};
?>

<div class="dark:bg-dark-800">
	<h1 class="text-lg mb-4 font-semibold">Billing</h1>

	@if (session('ok'))
		<div
				x-data="{ show: true }"
				x-init="setTimeout(() => show = false, 3000)"
				x-show="show"
				x-transition:leave="transition ease-in duration-300"
				x-transition:leave-start="opacity-100"
				x-transition:leave-end="opacity-0"
				class="mt-4 rounded-xl bg-emerald-50 text-emerald-900 px-4 py-3 text-sm">{{ session('ok') }}</div>
	@endif

	<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
		<!-- Subscription card -->
		<div class="md:col-span-2 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:bg-dark-800">
			<div class="flex items-center justify-between">
				<h2 class="text-lg font-semibold">Subscription</h2>
				@if($subscription)
					<span class="text-xs px-2 py-0.5 rounded-full {{ $subscription['on_grace_period'] ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-900' }}">
							{{ strtoupper($subscription['stripe_status']) }}
                    </span>
				@else
					<span class="text-xs px-2 py-0.5 rounded-full bg-neutral-100">Not subscribed</span>
				@endif
			</div>

			@if(!$subscription)
				<p class="mt-4 text-neutral-600">No active plan. Choose one on the <a
							href="{{ route('tenant.pricing', ['tenantSubdomain' => tenant()->subdomain]) }}"
							class="underline">pricing page</a>.</p>
			@else
				<dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
					<div>
						<dt class="text-neutral-500 dark:text-dark-400">Price</dt>
						<dd class="font-medium">{{ $subscription['price_display'] ?? '—' }}</dd>
					</div>
					<div>
						<dt class="text-neutral-500 dark:text-dark-400">Seats</dt>
						<dd class="font-medium">{{ $subscription['quantity'] ?? 1 }}</dd>
					</div>
					<div>
						<dt class="text-neutral-500 dark:text-dark-400">Trial ends</dt>
						<dd class="font-medium">{{ $subscription['trial_ends_at'] ?? '—' }}</dd>
					</div>
					<div>
						<dt class="text-neutral-500 dark:text-dark-400">Cancels at</dt>
						<dd class="font-medium">{{ $subscription['ends_at'] ?? '—' }}</dd>
					</div>
				</dl>

				<div class="mt-6 flex flex-wrap gap-3">
					@php($monthly = config('billing.prices.team_monthly'))
					@php($yearly  = config('billing.prices.team_yearly'))

					{{-- Monthly --}}
					@if($subscription && $subscription['stripe_price'] === $monthly)
						<span class="rounded-xl bg-blue-100 text-dark-800 px-3 py-2 text-sm">
							<x-icon
									name="check"
									class="inline-block w-4 h-4 mr-1" />
							Subscribed to Monthly</span>
					@else
						<button
								wire:click="swap(@js($monthly))"
								class="rounded-xl border px-3 py-2 text-sm hover:bg-dark-600 hover:cursor-pointer">
							Switch to Monthly
						</button>
					@endif

					{{-- Yearly --}}
					@if($subscription && $subscription['stripe_price'] === $yearly)
						<span class="rounded-xl bg-blue-100 text-dark-800 px-3 py-2 text-sm">
							<x-icon
									name="check"
									class="inline-block w-4 h-4 mr-1" />
							Subscribed to Yearly</span>
					@else
						<button
								wire:click="swap(@js($yearly))"
								class="rounded-xl border px-3 py-2 text-sm hover:bg-neutral-50"
						>
							Switch to Yearly
						</button>
					@endif
					{{-- Cancel / Resume --}}
					@if($subscription && !$subscription['on_grace_period'])
						<x-button
								icon="x-circle"
								sm
								wire:click="cancel"
								class="rounded-xl bg-rose-600 text-white px-3 py-2 text-sm hover:bg-rose-700"
						>
							Cancel Subscription
						</x-button>
					@elseif($subscription && $subscription['on_grace_period'])
						<button
								wire:click="resume"
								class="rounded-xl bg-black text-white px-3 py-2 text-sm hover:opacity-90"
						>
							Resume Subscription
						</button>
					@endif
				</div>

				<!-- Optional seat sync (bind to your own team size) -->
				{{--				<div class="mt-6">--}}
				{{--					<form--}}
				{{--							wire:submit.prevent="syncSeats($refs.qty.value)"--}}
				{{--							class="flex items-center gap-3">--}}
				{{--						<label class="text-sm text-neutral-600">Seats</label>--}}
				{{--						<input--}}
				{{--								x-ref="qty"--}}
				{{--								type="number"--}}
				{{--								min="1"--}}
				{{--								value="{{ $subscription['quantity'] ?? 1 }}"--}}
				{{--								class="w-24 rounded-lg border px-3 py-2 text-sm" />--}}
				{{--						<button--}}
				{{--								type="submit"--}}
				{{--								class="rounded-lg border px-3 py-2 text-sm hover:bg-neutral-50">Update--}}
				{{--						</button>--}}
				{{--					</form>--}}
				{{--				</div>--}}
			@endif
		</div>

		<!-- Payment method card -->
		<div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:bg-dark-800">
			<h2 class="text-lg font-semibold">Payment method</h2>
			@if($pm)
				<p class="mt-3 text-sm text-neutral-700 dark:text-dark-200">{{ strtoupper($pm['brand'] ?? 'CARD') }}
					•••• {{ $pm['last4'] }}</p>
				<p class="text-xs text-neutral-500 dark:text-dark-400">Exp {{ $pm['exp_month'] }}
				                                                       /{{ $pm['exp_year'] }}</p>
				<p class="mt-3 text-xs text-neutral-500 dark:text-dark-400">Update or add methods via the Customer
				                                                            Portal.</p>
				<button
						wire:click="portal"
						class="mt-3 w-full rounded-xl border px-3 py-2 text-sm hover:bg-neutral-50">Manage in Portal
				</button>
			@else
				<p class="mt-3 text-sm text-neutral-600">No default payment method on file.</p>
				<a
						href="{{ route('tenant.pricing', ['tenantSubdomain' => tenant()->subdomain]) }}"
						class="mt-3 inline-block rounded-xl bg-black text-white px-3 py-2 text-sm">Add a card</a>
			@endif
		</div>
	</div>

	<!-- Invoices -->
	<div class="mt-8 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:bg-dark-800">
		<div class="flex items-center justify-between">
			<h2 class="text-lg font-semibold">Invoices</h2>
			<span class="text-xs text-neutral-500 dark:text-dark-400">Showing latest {{ count($invoices) }}</span>
		</div>
		<div class="mt-4 overflow-x-auto">
			<table class="w-full text-sm">
				<thead class="text-left text-neutral-500 dark:text-dark-400">
				<tr>
					<th class="py-2">Date</th>
					<th class="py-2">Number</th>
					<th class="py-2">Amount</th>
					<th class="py-2">Status</th>
					<th class="py-2">Actions</th>
				</tr>
				</thead>
				<tbody class="divide-y">
				@forelse($invoices as $inv)
					<tr>
						<td class="py-2">{{ $inv['date'] }}</td>
						<td class="py-2">{{ $inv['number'] }}</td>
						<td class="py-2">${{ $inv['amount_display'] }} {{ $inv['currency'] }}</td>
						<td class="py-2">{{ ucfirst($inv['status']) }}</td>
						<td class="py-2">
							<a
									href="{{ $inv['hosted_invoice_url'] }}"
									target="_blank"
									class="underline">View</a>
							@if($inv['invoice_pdf'])
								<a
										href="{{ $inv['invoice_pdf'] }}"
										target="_blank"
										class="ml-3 underline">PDF</a>
							@endif
						</td>
					</tr>
				@empty
					<tr>
						<td
								class="py-6 text-neutral-500 dark:text-dark-400"
								colspan="5">No invoices yet.
						</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
	/**
	 * ROUTE: Add this to the tenant-aware group (see earlier message)
	 * Route::view('/billing', 'billing.index')->name('billing.index');
	 *
	 * BLADE WRAPPER: resources/views/billing/index.blade.php
	 * @extends('layouts.app')
	 * @section('content')
	 *     <livewire:billing-index />
	 * @endsection
	 *
	 * NOTES:
	 * - Uses Cashier helpers: subscription(), invoices(), defaultPaymentMethod().
	 * - Swap buttons read price IDs from config('billing.prices.*').
	 * - Customer Portal button uses Cashier's redirect helper.
	 * - Seat sync is optional; wire it to your own team size rules if needed.
	 */
?>

@once
	<script src="https://js.stripe.com/v3/"></script>
@endonce