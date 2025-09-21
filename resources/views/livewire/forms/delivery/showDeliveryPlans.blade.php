<?php

	use App\Models\Demand;

	new class extends \Livewire\Volt\Component {

		public ?Demand $demand = null;
		public bool $showViewDeliveryPlanModal = false;
		public bool $showAddContingencyModal = false;
		public ?int $selectedDeliveryPlan = null;

		public function mount(?int $demandId = null):void
		{
			if ($demandId) {
				$this->demand = app(\App\Services\Demand\DemandFetchingService::class)->getDemandById($demandId,
					['deliveryPlans']);
			}
		}

		public function showDeliveryPlan(int $deliveryPlanId):void
		{
			$this->selectedDeliveryPlan = $deliveryPlanId;
			$this->showViewDeliveryPlanModal = true;
		}

		public function startAddContingency(int $deliveryPlanId):void
		{
			$this->selectedDeliveryPlan = $deliveryPlanId;
			$this->showAddContingencyModal = true;
		}
	}

?>

<div class="space-y-3 bg-dark-100 dark:bg-dark-800 p-3 rounded-lg">
	@if($demand)
		<h3 class="text-lg font-semibold">{{ $demand->title }}</h3>
		<!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
		<!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->
		<ul
				role="list"
				class="divide-y divide-gray-100 dark:divide-white/5">
			@foreach($demand->deliveryPlans as $plan)
				<li class="flex justify-between gap-x-6 py-5">
					<div class="flex min-w-0 gap-x-4">

						<div class="min-w-0 flex-auto">
							<p class="text-sm/6 font-semibold text-gray-900 dark:text-white">
							<div class="flex items-center gap-2">
								<button
										wire:click="showDeliveryPlan({{ $plan->id }})"
										class="hover:underline">{{ $plan->title }}</button>
								@php($__contCount = is_array($plan->contingencies ?? null) ? count($plan->contingencies) : 0)
								<span
										class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200"
										title="Contingencies">{{ $__contCount }}</span>
							</div>
							</p>
							<p class="mt-1 flex text-xs/5 text-gray-500 dark:text-gray-400">
								<a
										href="mailto:leslie.alexander@example.com"
										class="truncate hover:underline">{{ $plan->creator->name }}</a>
							</p>
						</div>
					</div>
					<div>
						<div class="min-w-0 flex-auto">
							<p class="text-sm/6 font-semibold text-gray-900 dark:text-white">
								<span
										class="hover:underline">Scheduled:</span>
							</p>
							<p class="mt-1 flex text-xs/5 text-gray-500 dark:text-gray-400">
								<span
										class="truncate hover:underline">{{ $plan->scheduled_at }}</span>
							</p>
						</div>
					</div>
					<div>
						<div class="min-w-0 flex-auto">
							<p class="text-sm/6 font-semibold text-gray-900 dark:text-white">
								<span
										class="hover:underline">Window Starts At:</span>
							</p>
							<p class="mt-1 flex text-xs/5 text-gray-500 dark:text-gray-400">
								<span
										class="truncate hover:underline">{{ $plan->window_starts_at }}</span>
							</p>
						</div>
					</div>

					<div>
						<div class="min-w-0 flex-auto">
							<p class="text-sm/6 font-semibold text-gray-900 dark:text-white">
								<span
										class="hover:underline">Window Ends At:</span>
							</p>
							<p class="mt-1 flex text-xs/5 text-gray-500 dark:text-gray-400">
								<span
										class="truncate hover:underline">{{ $plan->window_ends_at }}</span>
							</p>
						</div>
					</div>
					<div class="flex shrink-0 items-center gap-x-6">
						<div class="hidden sm:flex sm:flex-col sm:items-end">
							<x-badge
									text="{{$plan->status}}"
									color="green" />
							{{--							<p class="text-sm/6 text-gray-900 dark:text-white">Co-Founder / CEO</p>--}}
							<p class="mt-1 text-xs/5 text-gray-500 dark:text-gray-400">Created
								<time datetime="2023-01-23T13:23Z">{{ $plan->created_at ? $plan->created_at->diffForHumans() : '' }}</time>
							</p>
						</div>
						<x-dropdown
								position="top-start"
								icon="ellipsis-vertical"
								static>
							<x-dropdown.items
									text="View"
									wire:click="showDeliveryPlan({{ $plan->id }})" />
							<x-dropdown.items
									text="Add Contingency"
									wire:click="startAddContingency({{ $plan->id }})" />
						</x-dropdown>
					</div>
				</li>
			@endforeach
		</ul>
	@endif
	<x-modal
			id="view-delivery-plan-modal"
			center
			z-index="z-60"
			title="View Delivery Plan"
			wire="showViewDeliveryPlanModal">
		<livewire:forms.delivery.show-delivery-plan
				:delivery-plan-id="$selectedDeliveryPlan"
				:key="'show-delivery-plan-'.$selectedDeliveryPlan" />
	</x-modal>

	<x-slide
			id="add-contingency-slide"
			z-index="z-60"
			left
			title="Add Contingency"
			x-cloak
			wire="showAddContingencyModal">
		<livewire:forms.delivery.create-contingencies
				:delivery-plan-id="$selectedDeliveryPlan"
				:key="'create-contingencies-'.$selectedDeliveryPlan" />
	</x-slide>
</div>
