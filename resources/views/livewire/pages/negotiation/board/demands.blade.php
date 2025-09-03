<?php

	use App\Livewire\Forms\CreateDemandForm;
	use App\Models\Demand;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\Demand\DemandDestructionService;
	use App\Services\Demand\DemandFetchingService;
	use App\Services\Negotiation\NegotiationFetchingService;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;

	/**
	 * Demands Component
	 *
	 * This Livewire component manages the display and manipulation of demands
	 * associated with a negotiation's primary subject. It handles creating,
	 * editing, and deleting demands, as well as listening for real-time updates
	 * through broadcast events.
	 */
	new class extends Component {
		/** @var bool Flag to control the visibility of the create demand modal */
		public bool $showCreateDemandModal = false;

		/** @var bool Flag to control the visibility of the edit demand modal */
		public bool $showEditDemandModal = false;

		/** @var Negotiation The negotiation being viewed */
		public Negotiation $negotiation;

		/** @var Subject The primary subject of the negotiation */
		public Subject $primarySubject;

		/** @var int The ID of the negotiation */
		public int $negotiationId;

 	/** @var Demand|null The demand being edited */
 	public $demandToEdit;

 	public CreateDemandForm $form;
	
 	/** @var string The field to sort demands by */
 	public string $sortBy = 'created_at';

		/**
		 * Initialize the component with the negotiation data
		 *
		 * @param  int  $negotiationId  The ID of the negotiation to load
		 *
		 * @return void
		 */
		public function mount($negotiationId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)->getNegotiationById($negotiationId);
			$this->primarySubject = $this->negotiation->primarySubject();
			$this->negotiationId = $this->negotiation->id;

			// Eager load demands with their relationships to prevent N+1 queries
			$this->primarySubject->load('demands');
		}

		/**
		 * Define the event listeners for this component
		 *
		 * @return array Array of event listeners mapped to handler methods
		 */
		public function getListeners():array
		{
			$tenantId = auth()->user()->tenant_id;
			return [
				"echo-private:private.negotiation.$tenantId.$this->negotiationId,.DemandCreated" => 'handleDemandCreated',
				"echo-private:private.negotiation.$tenantId.$this->negotiationId,.DemandUpdated" => 'handleDemandUpdated',
				"echo-private:private.negotiation.$tenantId.$this->negotiationId,.DemandDestroyed" => 'handleDemandUpdated',
				'refresh' => '$refresh',
			];
		}

		/**
		 * Handle the DemandCreated event by refreshing the demands collection
		 *
		 * @param  array  $data  Event data
		 *
		 * @return void
		 */
		public function handleDemandCreated(array $data):void
		{
			// Reload the primary subject with fresh demands data
			$this->primarySubject = $this->primarySubject->fresh(['demands']);

			// Force a refresh of the component
			$this->dispatch('refresh');

			// Ensure the component is re-rendered
			$this->render();
		}

		/**
		 * Handle the DemandUpdated or DemandDestroyed event by refreshing the demands collection
		 *
		 * @param  array  $data  Event data
		 *
		 * @return void
		 */
		public function handleDemandUpdated(array $data):void
		{
			$this->dispatch('refresh');
		}

		public function handleDemandDestroyed(array $data)
		{
			$this->primarySubject = $this->primarySubject->fresh('demands');
		}

		/**
		 * Prepare a demand for editing and show the edit modal
		 *
		 * @param  int  $demandId  The ID of the demand to edit
		 *
		 * @return void
		 */
		public function editDemand($demandId):void
		{
			// Reset the demand being edited before setting a new one
			$this->demandToEdit = null;

			$demand = app(DemandFetchingService::class)->getDemandById($demandId);

			if ($demand) {
				$this->demandToEdit = $demand;
			}
			$this->showEditDemandModal = true;
		}

		/**
		 * Delete a demand by its ID
		 *
		 * @param  int  $demandId  The ID of the demand to delete
		 *
		 * @return void
		 */
		public function deleteDemand($demandId):void
		{
			app(DemandDestructionService::class)->deleteDemand($demandId);
		}

 	/**
 	 * Close all modal dialogs and refresh the component
 	 *
 	 * This method is triggered by the 'close-modal' event
 	 *
 	 * @return void
 	 */
 	#[On('close-modal')]
 	public function closeModal():void
 	{
 		$this->showCreateDemandModal = false;
 		$this->showEditDemandModal = false;

 		// Reload the primary subject with fresh demands data
 		$this->primarySubject = $this->primarySubject->fresh(['demands']);

 		// Force a refresh of the component
 		$this->dispatch('refresh');

 		// Ensure the component is re-rendered
 		$this->render();
 	}
	
 	/**
 	 * Update the sort field and refresh the demands
 	 *
 	 * @param  string  $field  The field to sort by
 	 *
 	 * @return void
 	 */
 	public function updateSort(string $field):void
 	{
 		$this->sortBy = $field;
 	}

 	/**
 	 * Get the sorted demands collection
 	 *
 	 * @return \Illuminate\Support\Collection
 	 */
 	public function getSortedDemands():\Illuminate\Support\Collection
 	{
 		return $this->primarySubject->demands->sortBy($this->sortBy);
 	}

 }

?>

<div
		class=""
		x-data="{ showDemands: true }">
	<div class="bg-indigo-600 px-4 py-2 rounded-lg flex items-center justify-between">
		<h3 class="text-sm font-semibold">Demands <span
					x-show="!showDemands"
					x-transition>({{ $primarySubject->demands->count() }})</span></h3>
		<div class="flex items-center gap-2">
			<select
					wire:model.live="sortBy"
					wire:change="updateSort($event.target.value)"
					class="text-xs py-1 pl-2 pr-7 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500">
				<option value="created_at">Sort by Date</option>
				<option value="title">Sort by Title</option>
				<option value="priority_level">Sort by Priority</option>
				<option value="deadline_date">Sort by Deadline</option>
			</select>
			<x-button
					wire:click="$toggle('showCreateDemandModal')"
					color="white"
					sm
					flat
					icon="plus" />
			<x-button
					@click="showDemands = !showDemands"
					color="white"
					sm
					flat
					icon="chevron-up-down" />
		</div>

	</div>
	<div
			class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4"
			x-show="showDemands"
			x-transition>
		@if($primarySubject->demands->isNotEmpty())
			@foreach($this->getSortedDemands() as $demand)
				<div
						wire:key="tsui-card-{{ $demand->id }}">
					<x-card color="secondary">
						<x-slot:header>
							<div class="p-3 flex items-center justify-between bg-indigo-500">
								<div>
									<p class="capitalize font-semibold text-lg">{{ $demand->title }}</p>
									<p class="text-gray-300 text-xs">{{ $demand->channel?->label() ?? 'No Channel' }}</p>
								</div>
								<div class="text-right">
									<x-badge
											color="{{ $demand->priority_level?->color() ?? 'gray' }}"
											xs
											round
											icon="exclamation-circle"
											position="left">
										<span class="text-xs">{{ $demand->priority_level?->label() ?? 'No Priority' }}</span>
									</x-badge>
									<p class="text-gray-300 text-xs mt-1">{{ $demand->created_by ?? 'Unknown' }}</p>
								</div>
							</div>
						</x-slot:header>
						<p class="text-sm">
							{{ $demand->content }}
						</p>
						<x-slot:footer>
							<div class="flex items-center justify-between">
								<div class="flex items-center space-x-2">
									<x-badge
											color="teal"
											xs
											round
											icon="tag"
											position="left">
										<span class="text-xs">{{ $demand->category?->label() ?? 'No Category' }}</span>
									</x-badge>
									<x-badge
											color="{{ $demand->status?->color() ?? 'gray' }}"
											xs
											round>
										<span class="text-xs">{{ $demand->status?->label() ?? 'No Status' }}</span>
									</x-badge>
									@if($demand->deadline_date)
										<x-badge
												color="red"
												xs
												round
												icon="clock"
												position="left">
										<span class="text-xs">
											{{ $demand->deadline_date->format('M d, Y') }}
											@if($demand->deadline_time)
												{{ $demand->deadline_time }}
											@endif
										</span>
										</x-badge>
									@endif
								</div>
								<div>
									<x-button
											wire:click="editDemand({{ $demand->id }})"
											color="cyan"
											sm
											flat
											icon="pencil-square" />
									<x-button
											wire:click="deleteDemand({{ $demand->id }})"
											color="red"
											sm
											flat
											icon="trash" />
								</div>

							</div>

						</x-slot:footer>
					</x-card>
				</div>
			@endforeach
		@else
			<div class="col-span-3 text-center py-8">
				<p class="text-gray-500 mb-4">No demands available for this subject.</p>
				<p class="text-sm text-gray-400">Click the + button above to create a new demand.</p>
			</div>
		@endif

	</div>
	<x-modal
			id="create-demand-modal"
			center
			title="Create Demand"
			wire="showCreateDemandModal">
		<livewire:forms.demand.create-demand
				:subjectId="$primarySubject->id"
				:negotiationId="$negotiation->id" />
	</x-modal>
	<x-modal
			id="edit-demand-modal"
			center
			title="Edit Demand"
			wire="showEditDemandModal"
			x-on:hidden.window="$wire.closeModal()">
		@if($demandToEdit)
			<livewire:forms.demand.edit-demand
					:demand="$demandToEdit"
					:key="'demand-'.$demandToEdit->id" />
		@endif
	</x-modal>
</div>
