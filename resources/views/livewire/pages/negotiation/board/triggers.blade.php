<?php

	use App\Models\Trigger;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\Trigger\TriggerDestructionService;
	use App\Services\Trigger\TriggerFetchingService;
	use App\Services\Negotiation\NegotiationFetchingService;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;
	use TallStackUi\Traits\Interactions;

	/**
	 * Triggers Component
	 *
	 * This Livewire component manages the display and manipulation of triggers
	 * associated with a negotiation's primary subject. It handles creating,
	 * editing, and deleting triggers, as well as listening for real-time updates
	 * through broadcast events.
	 */
	new class extends Component {
		use Interactions;

		/** @var bool Flag to control the visibility of the create trigger modal */
		public bool $showCreateTriggerModal = false;

		/** @var bool Flag to control the visibility of the edit trigger modal */
		public bool $showEditTriggerModal = false;

		/** @var Negotiation The negotiation being viewed */
		public Negotiation $negotiation;

		/** @var Subject The primary subject of the negotiation */
		public Subject $primarySubject;

		/** @var int The ID of the negotiation */
		public int $negotiationId;

		/** @var Trigger|null The trigger being edited */
		public $triggerToEdit;

		/** @var string The field to sort triggers by */
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

			// Eager load triggers with their relationships to prevent N+1 queries
			$this->primarySubject->load('triggers');
		}

		/**
		 * Define the event listeners for this component
		 *
		 * @return array Array of event listeners mapped to handler methods
		 */
		public function getListeners()
		{
			$tenantId = auth()->user()->tenant_id;
			return [
				'echo-private:'.\App\Support\Channels\Negotiation::negotiationTriggers($this->negotiationId).',.'.\App\Support\EventNames\NegotiationEventNames::TRIGGER_CREATED => 'handleTriggerCreated',
				'echo-private:'.\App\Support\Channels\Negotiation::negotiationTriggers($this->negotiationId).',.'.\App\Support\EventNames\NegotiationEventNames::TRIGGER_DELETED => 'handleTriggerDeleted',
				'echo-private:'.\App\Support\Channels\Negotiation::negotiationTriggers($this->negotiationId).',.'.\App\Support\EventNames\NegotiationEventNames::TRIGGER_UPDATED => 'handleTriggerUpdated',

//				"echo-private:private.negotiation.$tenantId.$this->negotiationId,.TriggerCreated" => 'handleTriggerCreated',
				"echo-private:private.negotiation.$tenantId.$this->negotiationId,.TriggerUpdated" => 'handleTriggerUpdated',
				"echo-private:private.negotiation.$tenantId.$this->negotiationId,.TriggerDestroyed" => 'handleTriggerDestroyed',
			];
		}

		/**
		 * Handle the TriggerCreated event: show a toast and refresh
		 */
		public function handleTriggerCreated(array $data):void
		{
			$triggerId = $data['triggerId'] ?? $data['trigger'] ?? null;
			$trigger = $triggerId? app(\App\Services\Trigger\TriggerFetchingService::class)->getTrigger($triggerId) : null;

			if ($trigger) {
				$subjectName = $trigger->subject->name ?? ($this->primarySubject->name ?? 'the subject');
				$actor = $trigger->user ?? null;
				$title = $trigger->title ?? 'a trigger';
				if ($actor && $actor->id === auth()->id()) {
					$message = "You created a new '{$title}' trigger for {$subjectName}.";
				} elseif ($actor) {
					$message = "{$actor->name} created a new '{$title}' trigger for {$subjectName}.";
				} else {
					$message = "A new '{$title}' trigger was created for {$subjectName}.";
				}
			} else {
				$message = "A trigger has been created.";
			}

			$this->toast()->timeout()->info($message)->send();
			$this->primarySubject->load('triggers');
		}

		/**
		 * Handle the TriggerUpdated event: show a toast and refresh
		 */
		public function handleTriggerUpdated(array $data)
		{
			$triggerId = $data['triggerId'] ?? $data['trigger'] ?? null;
			$trigger = $triggerId? app(\App\Services\Trigger\TriggerFetchingService::class)->getTrigger($triggerId) : null;

			if ($trigger) {
				$subjectName = $trigger->subject->name ?? ($this->primarySubject->name ?? 'the subject');
				$actor = $trigger->user ?? null;
				$title = $trigger->title ?? 'a trigger';
				if ($actor && $actor->id === auth()->id()) {
					$message = "You updated the '{$title}' trigger for {$subjectName}.";
				} elseif ($actor) {
					$message = "{$actor->name} updated the '{$title}' trigger for {$subjectName}.";
				} else {
					$message = "The '{$title}' trigger was updated for {$subjectName}.";
				}
			} else {
				$message = "A trigger has been updated.";
			}

			$this->toast()->timeout()->info($message)->send();
			$this->primarySubject->load('triggers');
		}

		/**
		 * Handle the TriggerDestroyed event by sending a toast
		 */
		public function handleTriggerDeleted(array $data):void
		{
			$details = $data['details'] ?? null;
			if ($details) {
				$title = $details['title'] ?? 'a trigger';
				$createdBy = $details['createdBy'] ?? 'Someone';
				$subjectName = $details['subjectName'] ?? ($this->primarySubject->name ?? 'the subject');
				$message = "{$createdBy} deleted '{$title}' for {$subjectName}.";
			} else {
				$message = "A trigger has been deleted.";
			}
			$this->toast()->timeout()->info($message)->send();
			$this->primarySubject->load('triggers');
		}

		/**
		 * Prepare a trigger for editing and show the edit modal
		 *
		 * @param  int  $triggerId  The ID of the trigger to edit
		 *
		 * @return void
		 */
		public function editTrigger($triggerId):void
		{
			// Reset the trigger being edited before setting a new one
			$this->triggerToEdit = null;

			$trigger = app(TriggerFetchingService::class)->getTrigger($triggerId);
			if ($trigger) {
				$this->triggerToEdit = $trigger;
			}
			$this->showEditTriggerModal = true;
		}

		/**
		 * Delete a trigger by its ID
		 *
		 * @param  int  $triggerId  The ID of the trigger to delete
		 *
		 * @return void
		 */
		public function deleteTrigger($triggerId)
		{
			app(TriggerDestructionService::class)->deleteTrigger($triggerId);
		}

		/**
		 * Close all modal dialogs
		 *
		 * This method is triggered by the 'close-modal' event
		 *
		 * @return void
		 */
		#[On('close-modal')]
		public function closeModal():void
		{
			$this->showCreateTriggerModal = false;
			$this->showEditTriggerModal = false;
			$this->triggerToEdit = null; // Reset the trigger being edited
		}

		/**
		 * Update the sort field and refresh the triggers
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
		 * Get the sorted triggers collection
		 *
		 * @return \Illuminate\Support\Collection
		 */
		public function getSortedTriggers():\Illuminate\Support\Collection
		{
			return $this->primarySubject->triggers->sortBy($this->sortBy);
		}

	}

?>

<div
		class=""
		x-data="{ showTriggers: true }">
	<div class="bg-rose-600 dark:bg-rose-700 px-4 py-2 rounded-lg flex items-center justify-between">
		<h3 class="text-sm font-semibold text-white">Triggers <span
					x-show="!showTriggers"
					x-transition>({{ $primarySubject->triggers->count() }})</span></h3>
		<div class="flex items-center gap-2">
			<select
					wire:model.live="sortBy"
					wire:change="updateSort($event.target.value)"
					class="text-xs py-1 pl-2 pr-7 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500">
				<option
						class=""
						value="created_at">Sort by Date
				</option>
				<option value="title">Sort by Name</option>
				<option value="source">Sort by Source</option>
			</select>
			<x-button
					wire:click="$toggle('showCreateTriggerModal')"
					color="white"
					sm
					flat
					icon="plus" />
			<x-button
					@click="showTriggers = !showTriggers"
					color="white"
					sm
					flat
					icon="chevron-up-down" />
		</div>
	</div>
	<div
			class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4"
			x-show="showTriggers"
			x-transition>
		@if($primarySubject->triggers->isNotEmpty())
			@foreach($this->getSortedTriggers() as $trigger)
				<div
						wire:key="tsui-card-{{ $trigger->id }}">
					<x-card color="secondary">
						<x-slot:header>
							<div class="p-3 flex items-center justify-between bg-rose-400 dark:bg-rose-500 text-dark-100 rounded-t-lg">
								<div>
									<p class="capitalize font-semibold text-lg">{{ $trigger->title }}</p>
									<p class="text-gray-100 dark:text-gray-300 text-xs">{{ $trigger->source }}</p>
								</div>
								<div class="text-right">
									<x-subject.confidence-badge :confidence-score="$trigger->confidence_score" />
									<p class="text-gray-100 dark:text-gray-300 text-xs mt-1">{{ $trigger->user->name }}</p>
								</div>
							</div>
						</x-slot:header>
						<p class="text-sm">
							{{ $trigger->description }}
						</p>
						<x-slot:footer>
							<div class="flex items-center justify-between">
								<x-badge
										:color="App\Enums\Trigger\TriggerCategories::from($trigger->category)->color()"
										xs
										round
										icon="tag"
										position="left">
									<span class="text-xs">{{ App\Enums\Trigger\TriggerCategories::from($trigger->category)->label() }}</span>
								</x-badge>
								<div>
									<x-button
											wire:click="editTrigger({{ $trigger->id }})"
											color="cyan"
											sm
											flat
											icon="pencil-square" />
									<x-button
											wire:click="deleteTrigger({{ $trigger->id }})"
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
				<p class="text-gray-500 dark:text-gray-400 mb-4">No triggers available for this subject.</p>
				<p class="text-sm text-gray-400 dark:text-gray-500">Click the + button above to create a new
				                                                    trigger.</p>
			</div>
		@endif
	</div>
	<template x-teleport="body">
		<x-modal
				id="create-trigger-modal"
				center
				title="Create Trigger"
				wire="showCreateTriggerModal">
			<livewire:forms.trigger.create-trigger
					:subjectId="$primarySubject->id"
					:negotiationId="$negotiation->id" />
		</x-modal>
	</template>
	<template x-teleport="body">
		<x-modal
				id="edit-trigger-modal"
				center
				title="Edit Trigger"
				wire="showEditTriggerModal"
				x-on:hidden.window="$wire.closeModal()">
			@if($triggerToEdit)
				<livewire:forms.trigger.edit-trigger
						:trigger="$triggerToEdit"
						:key="'edit-trigger-'.$triggerToEdit->id" />
			@endif
		</x-modal>
	</template>
</div>
