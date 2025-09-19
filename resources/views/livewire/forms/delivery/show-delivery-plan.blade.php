<?php

	use App\Models\DeliveryPlan;
	use App\Models\Demand;
	use Livewire\Attributes\Computed;

	new class extends \Livewire\Volt\Component {
		public ?DeliveryPlan $currentPlan = null;
		public ?Demand $currentDemand = null;

		public function mount(?int $deliveryPlanId = null):void
		{
			if ($deliveryPlanId) {
				$this->currentPlan = app(\App\Services\DeliveryPlan\DeliveryPlanFetchingService::class)
					->getDeliveryPlan($deliveryPlanId);

				// Eager load useful relations
				if ($this->currentPlan) {
					$this->currentPlan->loadMissing(['creator', 'demands', 'subjects', 'hostages']);
					// Determine the associated demand (use the first if multiple)
					$this->currentDemand = $this->currentPlan->demands->first();
				}
			}
		}
	}

?>

<div>
	@if($currentPlan)
		<x-card>
			<x-slot:header>
				<div class="flex items-start justify-between pb-4">
					<div>
						<h2 class="text-xl font-semibold">{{ $currentPlan->title }}</h2>
						<p class="text-xs text-gray-500 dark:text-gray-400">Plan ID: #{{ $currentPlan->id }}</p>
					</div>
					<div class="text-right space-y-1">
						<x-badge
								:text="$currentPlan->status?->label() ?? 'Pending'"
								color="indigo" />
						<p class="text-xs text-gray-500 dark:text-gray-400">
							Created {{ optional($currentPlan->created_at)->diffForHumans() }}</p>
						@if($currentPlan->creator)
							<p class="text-xs text-gray-500 dark:text-gray-400">By {{ $currentPlan->creator->name }}</p>
						@endif
					</div>
				</div>
			</x-slot:header>

			<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
				<div class="col-span-1 md:col-span-2 space-y-4">
					<x-card shadow="sm">
						<x-slot:header>
							<h3 class="text-sm font-semibold">Schedule</h3>
						</x-slot:header>
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
							<div>
								<p class="text-gray-500 dark:text-gray-400">Scheduled At</p>
								<p class="font-medium">{{ optional($currentPlan->scheduled_at)->format('M d, Y g:i A') ?? '—' }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">Window Starts</p>
								<p class="font-medium">{{ $currentPlan->window_starts_at ?? '—' }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">Window Ends</p>
								<p class="font-medium">{{ $currentPlan->window_ends_at ?? '—' }}</p>
							</div>
						</div>
						@if(!empty($currentPlan->summary))
							<div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 text-sm">
								<p class="text-gray-500 dark:text-gray-400 mb-1">Summary</p>
								<p class="text-gray-700 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($currentPlan->summary, 300) }}</p>
							</div>
						@endif
					</x-card>

					@if(!empty($currentPlan->instructions) || !empty($currentPlan->route) || !empty($currentPlan->constraints))
						<x-card shadow="sm">
							<x-slot:header>
								<h3 class="text-sm font-semibold">Plan Details</h3>
							</x-slot:header>
							<div class="space-y-3 text-sm">
								@if(!empty($currentPlan->instructions))
									<div>
										<p class="text-gray-500 dark:text-gray-400 mb-1">Instructions</p>
										<ul class="list-disc ml-5 space-y-1">
											@foreach(($currentPlan->instructions ?? []) as $item)
												<li>{{ is_array($item) ? json_encode($item) : $item }}</li>
											@endforeach
										</ul>
									</div>
								@endif
								@if(!empty($currentPlan->route))
									<div>
										<p class="text-gray-500 dark:text-gray-400 mb-1">Route</p>
										<pre class="text-xs bg-gray-50 dark:bg-gray-800 p-2 rounded">{{ json_encode($currentPlan->route, JSON_PRETTY_PRINT) }}</pre>
									</div>
								@endif
								@if(!empty($currentPlan->constraints))
									<div>
										<p class="text-gray-500 dark:text-gray-400 mb-1">Constraints</p>
										<ul class="list-disc ml-5 space-y-1">
											@foreach(($currentPlan->constraints ?? []) as $item)
												<li>{{ is_array($item) ? json_encode($item) : $item }}</li>
											@endforeach
										</ul>
									</div>
								@endif
							</div>
						</x-card>
					@endif
				</div>

				<div class="col-span-1 space-y-4">
					<x-card shadow="sm">
						<x-slot:header>
							<h3 class="text-sm font-semibold">Linked Entities</h3>
						</x-slot:header>
						<div class="text-sm space-y-1">
							<p>
								<span class="text-gray-500 dark:text-gray-400">Subjects:</span> {{ $currentPlan->subjects->count() }}
							</p>
							<p>
								<span class="text-gray-500 dark:text-gray-400">Hostages:</span> {{ $currentPlan->hostages->count() }}
							</p>
							<p>
								<span class="text-gray-500 dark:text-gray-400">Demands:</span> {{ $currentPlan->demands->count() }}
							</p>
						</div>
					</x-card>

					<x-card shadow="sm">
						<x-slot:header>
							<h3 class="text-sm font-semibold">Associated Demand</h3>
						</x-slot:header>
						@if($currentDemand)
							<div class="space-y-1 text-xs">
								<p class="text-sm font-semibold leading-tight line-clamp-2">{{ $currentDemand->title }}</p>
								@if(!empty($currentDemand->content))
									<p class="text-gray-700 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($currentDemand->content, 160) }}</p>
								@endif
								<div class="flex flex-wrap gap-1">
									<x-badge
											:text="$currentDemand->category?->label() ?? 'No Category'"
											color="teal"
											xs />

								</div>
								<div class="grid grid-cols-1 gap-2 mt-2">
									<div>
										<p class="text-gray-500 dark:text-gray-400">Deadline</p>
										<p class="font-medium">
											@if($currentDemand->deadline_date)
												{{ $currentDemand->deadline_date->format('M d, Y') }} {{ $currentDemand->deadline_time ?? '' }}
											@else
												—
											@endif
										</p>
									</div>
								</div>
								@else
									<p class="text-sm text-gray-500">No associated demand found for this plan.</p>
						@endif
					</x-card>
				</div>
			</div>

			<x-slot:footer>
				<div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
					<p>Last Updated: {{ optional($currentPlan->updated_at)->diffForHumans() }}</p>
					<p>Negotiation ID: {{ $currentPlan->negotiation_id ?? '—' }}</p>
				</div>
			</x-slot:footer>
		</x-card>
	@else
		<div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">No delivery plan selected.</div>
	@endif

</div>
