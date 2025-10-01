<?php

	use App\Models\Hook;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\Hook\HookDestructionService;
	use App\Services\Hook\HookFetchingService;
	use App\Services\Negotiation\NegotiationFetchingService;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;
	use TallStackUi\Traits\Interactions;

	/**
	 * Hooks Component
	 *
	 * This Livewire component manages the display and manipulation of hooks
	 * associated with a negotiation's primary subject. It handles creating,
	 * editing, and deleting hooks, as well as listening for real-time updates
	 * through broadcast events.
	 */
	new class extends Component {
		use Interactions;

		/** @var bool Flag to control the visibility of the create hook modal */
		public bool $showCreateHookModal = false;

		/** @var bool Flag to control the visibility of the edit hook modal */
		public bool $showEditHookModal = false;

		/** @var Negotiation The negotiation being viewed */
		public Negotiation $negotiation;

		/** @var Subject The primary subject of the negotiation */
		public Subject $primarySubject;

		/** @var int The ID of the negotiation */
		public int $negotiationId;

		/** @var Hook|null The hook being edited */
		public $hookToEdit;

		/** @var string The field to sort hooks by */
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

			// Eager load hooks with their relationships to prevent N+1 queries
			$this->primarySubject->load('hooks');
		}

		/**
		 * Define the event listeners for this component
		 *
		 * @return array Array of event listeners mapped to handler methods
		 */
		public function getListeners()
		{
			$tenantId = tenant()->id;
			return [
				'echo-private:'.\App\Support\Channels\Negotiation::negotiationHook($this->negotiationId).',.'.\App\Support\EventNames\NegotiationEventNames::HOOK_CREATED => 'handleHookCreated',
				'echo-private:'.\App\Support\Channels\Negotiation::negotiationHook($this->negotiationId).',.'.\App\Support\EventNames\NegotiationEventNames::HOOK_DELETED => 'handleHookDeleted',
				'echo-private:'.\App\Support\Channels\Negotiation::negotiationHook($this->negotiationId).',.'.\App\Support\EventNames\NegotiationEventNames::HOOK_UPDATED => 'handleHookUpdated',
				'refresh' => '$refresh',
			];
		}

		/**
		 * Handle the HookCreated event by refreshing the hooks collection
		 *
		 * @param  array  $data  Event data
		 *
		 * @return void
		 */
		public function handleHookCreated(array $data):void
		{
			// Attempt to fetch the created hook (event key could be 'hookId' or 'hook')
			$hookId = $data['hookId'] ?? $data['hook'] ?? null;
			$hook = $hookId? app(\App\Services\Hook\HookFetchingService::class)->getHookById($hookId) : null;

			if ($hook) {
				$subjectName = $hook->subject->name ?? ($this->primarySubject->name ?? 'the subject');
				$actor = $hook->createdBy ?? null;
				$title = $hook->title ?? 'a hook';
				if ($actor && $actor->id === auth()->id()) {
					$message = "You created a new '{$title}' hook for {$subjectName}.";
				} elseif ($actor) {
					$message = "{$actor->name} created a new '{$title}' hook for {$subjectName}.";
				} else {
					$message = "A new '{$title}' hook was created for {$subjectName}.";
				}
			} else {
				$message = "A hook has been created.";
			}

			$this->toast()->timeout()->info($message)->send();
			$this->dispatch('refresh');
		}

		/**
		 * Handle the HookUpdated event by sending a toast and refreshing
		 *
		 * @param  array  $data  Event data
		 *
		 * @return void
		 */
		public function handleHookUpdated(array $data):void
		{
			$hookId = $data['hookId'] ?? $data['hook'] ?? null;
			$hook = $hookId? app(\App\Services\Hook\HookFetchingService::class)->getHookById($hookId) : null;

			if ($hook) {
				$subjectName = $hook->subject->name ?? ($this->primarySubject->name ?? 'the subject');
				$actor = $hook->createdBy ?? null;
				$title = $hook->title ?? 'a hook';
				if ($actor && $actor->id === auth()->id()) {
					$message = "You updated the '{$title}' hook for {$subjectName}.";
				} elseif ($actor) {
					$message = "{$actor->name} updated the '{$title}' hook for {$subjectName}.";
				} else {
					$message = "The '{$title}' hook was updated for {$subjectName}.";
				}
			} else {
				$message = "A hook has been updated.";
			}

			$this->toast()->timeout()->info($message)->send();
			$this->dispatch('refresh');
		}

		/**
		 * Handle the HookDestroyed event by sending a toast with details if available
		 */
		public function handleHookDeleted(array $data):void
		{
			$details = $data['details'] ?? null;
			if ($details) {
				$title = $details['title'] ?? 'a hook';
				$createdBy = $details['createdBy'] ?? 'Someone';
				$subjectName = $details['subjectName'] ?? ($this->primarySubject->name ?? 'the subject');
				$message = "{$createdBy} deleted '{$title}' for {$subjectName}.";
			} else {
				$message = "A hook has been deleted.";
			}
			$this->toast()->timeout()->info($message)->send();
			$this->dispatch('refresh');
		}

		/**
		 * Prepare a hook for editing and show the edit modal
		 *
		 * @param  int  $hookId  The ID of the hook to edit
		 *
		 * @return void
		 */
		public function editHook($hookId):void
		{
			// Reset the hook being edited before setting a new one
			$this->hookToEdit = null;

			$hook = app(HookFetchingService::class)->getHookById($hookId);
			if ($hook) {
				$this->hookToEdit = $hook;
			}
			$this->showEditHookModal = true;
		}

		/**
		 * Delete a hook by its ID
		 *
		 * @param  int  $hookId  The ID of the hook to delete
		 *
		 * @return void
		 */
		public function deleteHook($hookId):void
		{
			app(HookDestructionService::class)->deleteHook($hookId);
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
			$this->showCreateHookModal = false;
			$this->showEditHookModal = false;
			$this->hookToEdit = null; // Reset the hook being edited
		}

		/**
		 * Update the sort field and refresh the hooks
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
		 * Get the sorted hooks collection
		 *
		 * @return \Illuminate\Support\Collection
		 */
		public function getSortedHooks():\Illuminate\Support\Collection
		{
			return $this->primarySubject->hooks->sortBy($this->sortBy);
		}

	}

?>

<div
		class=""
		x-data="{ showHooks: true }">
	<div class="bg-primary-600 dark:bg-primary-700 px-4 py-2 rounded-lg flex items-center justify-between">
		<h3 class="text-sm font-semibold text-white">Hooks <span
					x-show="!showHooks"
					x-transition>({{ $primarySubject->hooks->count() }})</span></h3>
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
					wire:click="$toggle('showCreateHookModal')"
					color="white"
					sm
					flat
					icon="plus" />
			<x-button
					@click="showHooks = !showHooks"
					color="white"
					sm
					flat
					icon="chevron-up-down" />
		</div>
	</div>
	<div
			class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4"
			x-show="showHooks"
			x-transition>
		@if($primarySubject->hooks->isNotEmpty())
			@foreach($this->getSortedHooks() as $hook)
				<div
						wire:key="tsui-card-{{ $hook->id }}">
					<x-card>
						<x-slot:header>
							<div class="p-3 flex items-center justify-between bg-primary-400 dark:bg-primary-500 text-dark-100 rounded-t-lg">
								<div>
									<p class="capitalize font-semibold">{{ $hook->title }}</p>
									<p class="text-gray-100 dark:text-gray-300 text-xs">{{ $hook->source }}</p>
								</div>
								<div class="text-right">
									<div class="flex items-end gap-1">
										<x-subject.confidence-badge :confidence-score="$hook->confidence_score" />
									</div>
									<p class="text-gray-100 dark:text-gray-300 text-xs mt-1">{{ $hook->createdBy->name }}</p>
								</div>
							</div>
						</x-slot:header>
						<p class="text-sm">
							{{ $hook->description }}
						</p>
						<x-slot:footer>
							<div class="flex items-center justify-between">
								<x-badge
										color="teal"
										xs
										round
										icon="tag"
										position="left"><span class="text-xs">{{ $hook->category->label() }}</span>
								</x-badge>
								<div>
									<x-button
											wire:click="editHook({{ $hook->id }})"
											color="cyan"
											sm
											flat
											icon="pencil-square" />
									<x-button
											wire:click="deleteHook({{ $hook->id }})"
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
				<p class="text-gray-500 dark:text-gray-400 mb-4">No hooks available for this subject.</p>
				<p class="text-sm text-gray-400 dark:text-gray-500">Click the + button above to create a new hook.</p>
			</div>
		@endif

	</div>
	<x-modal
			id="create-hook-modal"
			center
			title="Create Hook"
			wire="showCreateHookModal">
		<livewire:forms.hook.create-hook
				:subjectId="$primarySubject->id"
				:negotiationId="$negotiation->id" />
	</x-modal>
	<x-modal
			id="edit-hook-modal"
			center
			title="Edit Hook"
			wire="showEditHookModal"
			x-on:hidden.window="$wire.closeModal()">
		@if($hookToEdit)
			<livewire:forms.hook.edit-hook
					:hook="$hookToEdit"
					:key="'hook-'.$hookToEdit->id" />
		@endif
	</x-modal>
</div>
