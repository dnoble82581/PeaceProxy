<?php

	use App\Livewire\Forms\CreateDemandForm;
	use App\Models\Demand;
	use App\Support\Channels\Negotiation;
	use App\Support\EventNames\NegotiationEventNames;
	use TallStackUi\Traits\Interactions;

	new class extends \Livewire\Volt\Component {
		use Interactions;

		public int $negotiationId;
		public \App\Models\Negotiation $negotiation;
		public CreateDemandForm $form;
		public ?Demand $demandToView = null;
		public bool $showViewDemandModal = false;


		public function mount(int $negotiationId)
		{
			$this->negotiationId = $negotiationId;
			if ($this->negotiationId) {
				$this->negotiation = app(\App\Services\Negotiation\NegotiationFetchingService::class)->getNegotiationById($this->negotiationId,
					['demands']);
			}
		}

		public function viewDemand(int $demandId)
		{
			$this->demandToView = app(\App\Services\Demand\DemandFetchingService::class)->getDemandById($demandId,
				['subject', 'createdBy']);

			$this->showViewDemandModal = true;
		}

		public function getListeners()
		{
			$tenantId = tenant()->id;
			$negotiationId = $this->negotiationId;
			return [
				'echo-private:'.Negotiation::negotiationDemand($negotiationId).',.'.NegotiationEventNames::DEMAND_CREATED => 'handleDemandCreated',
			];
		}

		public function handleDemandCreated(array $event):void
		{
			$this->negotiation->load('demands');

			$demand = app(\App\Services\Demand\DemandFetchingService::class)->getDemandById($event['demandId']);

			if (!$demand) {
				return;
			}

			$messageFactory = app(\App\Factories\MessageFactory::class);
			$message = $messageFactory->generateMessage($demand, 'DemandCreated');

			$this->toast()->timeout()->info($message)->send();
		}

		public function handleDemandDeleted(array $event)
		{
			$this->negotiation->load('demands');
		}

		#[\Livewire\Attributes\On('closeViewDemandModal')]
		public function closeViewDemandModal()
		{
			$this->showViewDemandModal = false;
		}
	}

?>

<div>
	<div class="px-4 sm:px-6 lg:px-8">
		<div class="flow-root">
			<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
				<div class="inline-block min-w-full py-1">
					<div class="overflow-hidden shadow-sm outline-1 outline-black/5 sm:rounded-lg dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
						<table class="relative min-w-full divide-y divide-gray-300 dark:divide-white/15">
							<thead class="bg-dark-50 dark:bg-dark-800/75">
							<tr>
								<th
										scope="col"
										class="py-2 pr-3 pl-4 text-left text-xs font-semibold text-dark-900 sm:pl-6 dark:text-gray-200">
									Title
								</th>
								<th
										scope="col"
										class="px-3 py-2 text-left text-xs font-semibold text-dark-900 dark:text-gray-200">
									Deadline
								</th>
								<th
										scope="col"
										class="px-3 py-2 text-left text-xs font-semibold text-dark-900 dark:text-gray-200">
									Category
								</th>

								<th
										scope="col"
										class="py-3.5 pr-4 pl-3 sm:pr-6">
									<span class="sr-only">Edit</span>
								</th>
							</tr>
							</thead>
							<tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-dark-800/50">
							@foreach($negotiation->demands as $demand)
								<tr>
									<td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-dark-900 sm:pl-6 dark:text-white">
										{{ $demand->title }}
									</td>
									<td class="px-3 py-4 text-sm whitespace-nowrap text-dark-500 dark:text-dark-400">
										{{ $demand->deadline_date->format('M d') }} {{ $demand->deadline_time }}
									</td>
									<td class="px-3 py-4 text-sm whitespace-nowrap text-dark-500 dark:text-dark-400">
										<x-badge :text="$demand->category->label()" />
									</td>

									<td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6 flex items-center gap-2 justify-end">
										<x-button.circle
												wire:click="viewDemand({{ $demand->id }})"
												color="emerald"
												icon="eye"
												sm />
										<button class="bg-teal-700 hover:bg-teal-600/90 hover:cursor-pointer rounded-full p-[3px] hover:transition-colors easing-in-out duration-150">
											<x-ui.delivery-van-svg />
										</button>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<x-modal
			id="view-demand-modal"
			center
			title="View Demand"
			wire="showViewDemandModal"
			x-on:hidden.window="$wire.closeModal()">
		@if($demandToView)
			<livewire:forms.demand.view-demand
					:demand="$demandToView"
					:key="'demand-'.$demandToView->id" />
		@endif
	</x-modal>
</div>
