<?php

	use App\Enums\Objective\Priority;
	use App\Enums\Objective\Status;
	use App\Models\Objective;
	use App\Models\User;
	use App\DTOs\Objective\ObjectiveDTO;
	use App\DTOs\Pin\PinDTO;
	use App\Services\Objective\ObjectiveCreationService;
	use App\Services\Objective\ObjectiveUpdatingService;
	use App\Services\Objective\ObjectiveDestructionService;
	use App\Services\Pin\PinCreationService;
	use App\Services\Pin\PinDeletionService;
	use App\Services\Pin\PinFetchingService;
	use App\Support\Channels\Negotiation;
	use App\Support\EventNames\NegotiationEventNames;
	use Illuminate\Support\Collection;
	use Livewire\Attributes\Computed;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Auth;
	use TallStackUi\Traits\Interactions;

	new class extends Component {
		public ?int $negotiation_id = null;
		public ?string $objective = null;
		public string $priority = Priority::medium->value;
		public ?int $editingId = null;
		public bool $showForm = false;
		public $pinnedObjectives = [];
		public int $tenantId;

		use Interactions;


		public function mount(int $negotiationId = null):void
		{
			$this->negotiation_id = $negotiationId;
			$this->tenantId = tenant()->id;
			$this->loadPinnedObjectives();
		}

		public function loadPinnedObjectives():void
		{
			$this->pinnedObjectives = [];
			$pins = app(PinFetchingService::class)->getPins();

			foreach ($pins as $pin) {
				if ($pin->pinnable_type === 'App\\Models\\Objective') {
					$this->pinnedObjectives[$pin->pinnable_id] = true;
				}
			}
		}

		public function pinObjective($objectiveId):void
		{
			$objective = Objective::findOrFail($objectiveId);

			if ($objective) {
				$pinDTO = new PinDTO(
					null,
					auth()->user()->tenant_id,
					auth()->id(),
					'App\\Models\\Objective',
					$objectiveId
				);

				app(PinCreationService::class)->createPin($pinDTO);
				event(new \App\Events\Pin\ObjectivePinnedEvent(tenant()->id, $objectiveId));
				$this->loadPinnedObjectives();
			}
		}

		public function unpinObjective($objectiveId):void
		{
			app(PinDeletionService::class)->deletePinByPinnable('App\\Models\\Objective', $objectiveId);
			event(new \App\Events\Pin\ObjectiveUnpinnedEvent(tenant()->id, $objectiveId));
			$this->loadPinnedObjectives();
		}

		public function isPinned($objectiveId):bool
		{
			return isset($this->pinnedObjectives[$objectiveId]);
		}

		public function getListeners()
		{
			$negotiationId = $this->negotiation_id;
			return [
				"echo-private:tenants.$this->tenantId.notifications,.ObjectivePinned" => 'loadPinnedObjectives',
				"echo-private:tenants.$this->tenantId.notifications,.ObjectiveUnpinned" => 'loadPinnedObjectives',
				'echo-private:'.Negotiation::negotiationObjective($negotiationId).',.'.NegotiationEventNames::OBJECTIVE_CREATED => 'handleObjectiveCreated',
				'echo-private:'.Negotiation::negotiationObjective($negotiationId).',.'.NegotiationEventNames::OBJECTIVE_DELETED => 'handleObjectiveDeleted',
				'echo-private:'.Negotiation::negotiationObjective($negotiationId).',.'.NegotiationEventNames::OBJECTIVE_UPDATED => 'handleObjectiveUpdated',
			];
		}

		public function handleObjectiveCreated(array $event):void
		{
			$this->dispatch('$refresh');

			$objective = app(\App\Services\Objective\ObjectiveFetchingService::class)->fetchObjectiveById($event['objectiveId']);

			if (!$objective) {
				return;
			}

			$messageFactory = app(\App\Factories\MessageFactory::class);
			$message = $messageFactory->generateMessage($objective, 'ObjectiveCreated');

			$this->toast()->timeout()->info($message)->send();
		}

		public function handleObjectiveDeleted(array $event)
		{
			$this->dispatch('$refresh');

			$objectiveLabel = $event['objectiveLabel'];
			$objectivePriority = $event['priority'];
			$actorName = $event['actorName'];

			if ($event['actorId'] == authUser()->id) {
				$message = "You deleted a {$objectivePriority} priority objective";
			} else {
				$message = "{$actorName} deleted a {$objectivePriority} priority objective called {$objectiveLabel}.";
			}

			$this->toast()->timeout()->info($message)->send();
		}

		public function handleObjectiveUpdated(array $event):void
		{
			$this->dispatch('$refresh');

			$objective = app(\App\Services\Objective\ObjectiveFetchingService::class)->fetchObjectiveById($event['objectiveId']);

			if (!$objective) {
				return;
			}

			$messageFactory = app(\App\Factories\MessageFactory::class);
			$message = $messageFactory->generateMessage($objective, 'ObjectiveUpdated');

			$this->toast()->timeout()->info($message)->send();

		}

		#[Computed]
		public function objectives():Collection
		{
			return Objective::query()
				->where('negotiation_id', $this->negotiation_id)
				->with(['createdBy', 'completedBy'])
				->latest()
				->get();
		}

		public function save():void
		{
			$this->validate([
				'objective' => 'required|string|min:3',
				'priority' => 'required|string|in:'.implode(',', array_map(fn($p) => $p->value, Priority::cases())),
			]);

			if ($this->editingId) {
				// Get the existing objective to preserve other fields
				$objective = Objective::findOrFail($this->editingId);

				// Create an ObjectiveDTO with updated fields
				$objectiveDTO = new ObjectiveDTO(
					id: $this->editingId,
					tenant_id: $objective->tenant_id,
					negotiation_id: $objective->negotiation_id,
					created_by_id: $objective->created_by_id,
					priority: $this->priority,
					status: $objective->status->value,
					objective: $this->objective,
					created_at: now(),
					updated_at: now()
				);

				// Use the service to update the objective
				app(ObjectiveUpdatingService::class)->updateObjective($objectiveDTO, $this->editingId);

				$this->dispatch('notify', [
					'message' => 'Objective updated successfully!',
					'type' => 'success',
				]);
			} else {
				// Create an ObjectiveDTO
				$objectiveDTO = new ObjectiveDTO(
					tenant_id: authUser()->tenant_id,
					negotiation_id: $this->negotiation_id,
					created_by_id: auth()->id(),
					priority: $this->priority,
					status: Status::pending->value,
					objective: $this->objective,
					created_at: now(),
					updated_at: now()
				);

				// Use the service to create the objective
				app(ObjectiveCreationService::class)->createObjective($objectiveDTO);

				$this->dispatch('notify', [
					'message' => 'Objective created successfully!',
					'type' => 'success',
				]);
			}

			$this->reset('objective', 'priority', 'editingId', 'showForm');
		}

		public function edit(int $id):void
		{
			$objective = Objective::findOrFail($id);
			$this->editingId = $id;
			$this->objective = $objective->objective;
			$this->priority = $objective->priority->value;
			$this->showForm = true;
		}

		public function getPriorityOptions():array
		{
			return array_map(function ($priority) {
				return [
					'value' => $priority->value,
					'label' => $priority->label(),
				];
			}, Priority::cases());
		}


		public function delete(int $id):void
		{
			// Use the service to delete the objective
			app(ObjectiveDestructionService::class)->deleteObjective($id);
		}

		public function complete(int $id):void
		{
			// Get the existing objective to preserve other fields
			$objective = Objective::findOrFail($id);

			// Create an ObjectiveDTO with updated fields
			$objectiveDTO = new ObjectiveDTO(
				id: $id,
				tenant_id: $objective->tenant_id,
				negotiation_id: $objective->negotiation_id,
				created_by_id: $objective->created_by_id,
				priority: $objective->priority->value,
				status: Status::completed->value,
				objective: $objective->objective
			);

			// Use the service to update the objective
			app(ObjectiveUpdatingService::class)->updateObjective($objectiveDTO, $id);

			$this->dispatch('notify', [
				'message' => 'Objective marked as complete!',
				'type' => 'success',
			]);
		}

		public function toggleForm():void
		{
			$this->showForm = !$this->showForm;
			if (!$this->showForm) {
				$this->reset('objective', 'priority', 'editingId');
			}
		}

		public function cancelEdit():void
		{
			$this->reset('objective', 'priority', 'editingId', 'showForm');
		}
	}

?>

<div>
	<div class="mb-4 flex justify-between items-center">
		<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Objectives</h2>
		<x-button
				wire:click="toggleForm"
				color="primary"
				icon="plus"
				sm>
			{{ $showForm ? 'Cancel' : 'Add Objective' }}
		</x-button>
	</div>

	@if($showForm)
		<div class="bg-white dark:bg-dark-700 p-4 rounded-lg shadow mb-6">
			<form wire:submit="save">
				<div class="space-y-4">
					<div>
						<x-textarea
								label="Objective"
								wire:model="objective"
								id="objective"
								class="mt-1 block w-full"
								rows="2"
								placeholder="Enter objective description" />
					</div>

					<div>
						<x-select.styled
								label="Priority"
								wire:model.defer="priority"
								id="priority"
								:options="$this->getPriorityOptions()"
								option-label="label"
								option-value="value"
								placeholder="Select a Priority"
						/>
					</div>

					<div class="flex justify-end space-x-2">
						<x-button
								wire:click="cancelEdit"
								type="button"
								color="secondary">Cancel
						</x-button>
						<x-button
								type="submit"
								color="primary">{{ $editingId ? 'Update' : 'Create' }}</x-button>
					</div>
				</div>
			</form>
		</div>
	@endif

	@if($this->objectives->isEmpty())
		<div class="text-center py-8">
			<p class="text-gray-500 dark:text-gray-400">No objectives found. Create your first objective!</p>
		</div>
	@else
		<ul
				role="list"
				class="divide-y divide-gray-100 dark:divide-white/5">
			@foreach($this->objectives as $objective)
				<li class="flex items-center justify-between gap-x-6 py-5">
					<div class="min-w-0">
						<div class="flex items-start gap-x-3">
							<p class="text-sm/6 font-semibold text-gray-900 dark:text-white">{{ $objective->objective }}</p>
							<x-badge
									text="{{ $objective->status->label() }}"
									color="{{ $objective->status->color() }}" />
							<x-badge
									text="{{ $objective->priority->label() }}"
									color="{{ $objective->priority->color() }}" />
						</div>
						<div class="mt-1 flex items-center gap-x-2 text-xs/5 text-gray-500 dark:text-gray-400">
							<p class="whitespace-nowrap">
								<time datetime="{{ $objective->created_at ? $objective->created_at->toIso8601String() : now()->toIso8601String() }}">{{ $objective->created_at ? $objective->created_at->format('F j, Y') : now()->format('F j, Y') }}</time>
							</p>
							<svg
									viewBox="0 0 2 2"
									class="size-0.5 fill-current">
								<circle
										r="1"
										cx="1"
										cy="1" />
							</svg>
							<p class="truncate">Created by {{ $objective->createdBy->name ?? 'Unknown' }}</p>

							@if($objective->status === 'completed' && $objective->completed_at)
								<svg
										viewBox="0 0 2 2"
										class="size-0.5 fill-current">
									<circle
											r="1"
											cx="1"
											cy="1" />
								</svg>
								<p class="whitespace-nowrap">
									Completed on
									<time datetime="{{ $objective->completed_at ? $objective->completed_at->toIso8601String() : now()->toIso8601String() }}">{{ $objective->completed_at ? $objective->completed_at->format('F j, Y') : now()->format('F j, Y') }}</time>
								</p>
								<svg
										viewBox="0 0 2 2"
										class="size-0.5 fill-current">
									<circle
											r="1"
											cx="1"
											cy="1" />
								</svg>
								<p class="truncate">Completed by {{ $objective->completedBy->name ?? 'Unknown' }}</p>
							@endif
						</div>
					</div>
					<div class="flex flex-none items-center gap-x-4">
						<x-dropdown
								icon="ellipsis-vertical"
								static>
							<x-dropdown.items
									icon="pencil-square">
								<button
										@if(authUser()->cannot('update', $objective))
											disabled
										@endif
										wire:click="edit({{ $objective->id }})"
										class="w-full hover:cursor-pointer disabled:cursor-not-allowed
									disabled:text-gray-400 text-left">
									Edit
								</button>
							</x-dropdown.items>

							@if($objective->status !== 'completed')
								<x-dropdown.items
										wire:click="complete({{ $objective->id }})"
										icon="check">Complete
								</x-dropdown.items>
							@endif
							@if($this->isPinned($objective->id))
								<x-dropdown.items
										wire:click="unpinObjective({{ $objective->id }})"
										icon="star"
										color="amber">Unpin
								</x-dropdown.items>
							@else
								<x-dropdown.items
										wire:click="pinObjective({{ $objective->id }})"
										icon="star">Pin
								</x-dropdown.items>
							@endif
							<x-dropdown.items
									wire:click="delete({{ $objective->id }})"
									separator
									icon="trash">Delete
							</x-dropdown.items>
						</x-dropdown>
					</div>
				</li>
			@endforeach
		</ul>
	@endif
</div>