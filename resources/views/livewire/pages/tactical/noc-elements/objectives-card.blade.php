<?php

	use App\Enums\Objective\Priority;
	use App\Enums\Objective\Status;
	use App\Models\Objective;
	use App\DTOs\Objective\ObjectiveDTO;
	use App\Services\Objective\ObjectiveCreationService;
	use App\Services\Objective\ObjectiveUpdatingService;
	use App\Services\Objective\ObjectiveDestructionService;
	use App\Support\Channels\Negotiation;
	use App\Support\EventNames\NegotiationEventNames;
	use Livewire\Attributes\Computed;
	use Livewire\Volt\Component;
	use Illuminate\Support\Collection;

	new class extends Component {

		use \TallStackUi\Traits\Interactions;

		public int $negotiationId;

		// form state
		public ?int $editingId = null;
		public bool $showForm = false;
		public ?string $objective = null;
		public string $priority = Priority::medium->value;

		public function mount(int $negotiationId):void
		{
			$this->negotiationId = (int) $negotiationId;
		}

		#[Computed]
		public function objectives():Collection
		{
			return Objective::query()
				->where('negotiation_id', $this->negotiationId)
				->latest()
				->get();
		}

		public function getPriorityOptions():array
		{
			return array_map(function ($p) {
				return [
					'value' => $p->value,
					'label' => $p->label(),
				];
			}, Priority::cases());
		}

		public function getListeners():array
		{
			$tenantId = (int) (tenant()->id ?? 0);
			$nId = (int) $this->negotiationId;
			$negotiationId = $this->negotiationId;
			return [
				"echo-private:private.negotiation.$tenantId.$nId,.ObjectiveCreated" => '$refresh',
				'echo-private:'.Negotiation::negotiationObjective($negotiationId).',.'.NegotiationEventNames::OBJECTIVE_CREATED => 'handleObjectiveCreated',
				'echo-private:'.Negotiation::negotiationObjective($negotiationId).',.'.NegotiationEventNames::OBJECTIVE_DELETED => 'handleObjectiveDeleted',
			];
		}

		public function handleObjectiveCreated()
		{
//			logger('Objective Created');
		}

		public function startCreate():void
		{
			$this->reset('editingId');
			$this->objective = '';
			$this->priority = Priority::medium->value;
			$this->showForm = true;
		}

		public function edit(int $id):void
		{
			$obj = Objective::findOrFail($id);
			$this->editingId = $id;
			$this->objective = $obj->objective;
			$this->priority = $obj->priority->value;
			$this->showForm = true;
		}

		public function save():void
		{
			$this->validate([
				'objective' => 'required|string|min:3',
				'priority' => 'required|string|in:'.implode(',', array_map(fn($p) => $p->value, Priority::cases())),
			]);

			if ($this->editingId) {
				$existing = Objective::findOrFail($this->editingId);
				$dto = new ObjectiveDTO(
					id: $this->editingId,
					tenant_id: $existing->tenant_id,
					negotiation_id: $existing->negotiation_id,
					created_by_id: $existing->created_by_id,
					priority: $this->priority,
					status: $existing->status->value,
					objective: $this->objective,
					updated_at: now()
				);
				app(ObjectiveUpdatingService::class)->updateObjective($dto, $this->editingId);
				$this->toast()->success("Successfully updated Objective")->send();

			} else {
				$dto = new ObjectiveDTO(
					tenant_id: authUser()->tenant_id,
					negotiation_id: $this->negotiationId,
					created_by_id: auth()->id(),
					priority: $this->priority,
					status: Status::pending->value,
					objective: $this->objective,
					created_at: now(),
					updated_at: now()
				);
				$objective = app(ObjectiveCreationService::class)->createObjective($dto);

				$label = $objective->priority->label();

				$this->toast()->info("You created a {$label} priority objective.")->send();
			}

			$this->reset('editingId', 'objective', 'priority', 'showForm');
		}

		public function delete(int $id):void
		{
			$is_deleted = app(ObjectiveDestructionService::class)->deleteObjective($id);
			if ($is_deleted) {
				$this->toast()->success('You successfully deleted an objective')->send();
			} else {
				$this->toast()->timeout()->error('There was a problem when trying to delete your objective.')->send();
			}
		}

		public function cancel():void
		{
			$this->reset('editingId', 'objective', 'priority');
			$this->showForm = false;
		}
	};

?>

<x-card
		class="h-full">
	<x-slot:header>
		<div class="flex items-center justify-between p-2">
			<h3 class="leading-tight">
				Objectives
			</h3>
			<x-button.circle
					sm
					icon="plus"
					title="Create"
					wire:click="startCreate" />
		</div>
	</x-slot:header>
	<template x-teleport="body">
		<x-modal wire="showForm">
			<x-card header="{{ $editingId ? 'Edit Objective' : 'Create Objective' }}">
				<div class="p-4 space-y-3">
					<x-input
							label="Objective Label"
							wire:model.defer="objective"
							placeholder="Enter objective..." />
					<x-select.styled
							label="Priority"
							wire:model.defer="priority"
							:options="$this->getPriorityOptions()"
							placeholder="Priority" />
				</div>
				<x-slot:footer>
					<div class="flex justify-end gap-2">
						<x-button
								sm
								wire:click="cancel"
								color="slate">Cancel
							</x-button>
						<x-button
								sm
								primary
								wire:click="save">{{ $editingId ? 'Update' : 'Create' }}</x-button>
					</div>
				</x-slot:footer>
			</x-card>
		</x-modal>
	</template>

	<div class="flow-root overflow-y-scroll h-[10rem]">
		<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
			<div class="inline-block min-w-full align-middle sm:px-4 lg:px-6">
				<div class="shadow-sm outline-1 outline-black/5 sm:rounded-lg dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
					<table class="relative min-w-full divide-y divide-gray-300 dark:divide-white/15">
						<thead class="bg-dark-50 dark:bg-dark-800/75 h-10">
						<tr class="">
							<th
									scope="col"
									class="py-1 pr-3 pl-4 text-left text-xs font-semibold text-gray-900 dark:text-dark-200">
								Objective
							</th>
							<th
									scope="col"
									class="py-1 pr-3 pl-4 text-left text-xs font-semibold text-gray-900 sm:pl-2 dark:text-dark-200">
								Priority
							</th>
							<th
									scope="col"
									class="py-1 pr-3 pl-4 text-left text-xs font-semibold text-gray-900 sm:pl-2 dark:text-dark-200">
								Status
							</th>
							<th
									scope="col"
									class="py-1 pr-3 pl-4 text-left text-xs font-semibold text-gray-900 sm:pl-2 dark:text-dark-200">
								Actions
							</th>
						</tr>
						</thead>
						<tbody class="divide-y divide-dark-200 bg-white dark:divide-white/10 dark:bg-dark-800/50">
						@forelse($this->objectives as $obj)
							<tr>
								<td
										class="py-2 pr-3 pl-4 text-xs font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">
									{{ $obj->objective }}</td>
								<td class="px-1 py-2 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
									<x-badge
											:text="$obj->priority->label()"
											:color="$obj->priority->value === 'high' ? 'rose' : ($obj->priority->value === 'low' ? 'slate' : 'amber')" />
								</td>
								<td class="px-1 py-2 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
									<x-badge
											:text="$obj->status->label()"
											:color="$obj->status->value === 'completed' ? 'teal' : 'blue'" />
								</td>
								<td class="px-1 py-2 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
									<div class="inline-flex items-center gap-2">
										<x-button.circle
												sm
												icon="check"
												title="Complete"
												wire:click="complete({{ $obj->id }})" />
										<x-button.circle
												sm
												icon="pencil"
												title="Edit"
												wire:click="edit({{ $obj->id }})" />
										<x-button.circle
												sm
												icon="trash"
												color="rose"
												title="Delete"
												wire:click="delete({{ $obj->id }})" />
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td
										colspan="5"
										class="px-4 py-4 text-center text-xs text-gray-500 dark:text-dark-300">No
								                                                                               objectives
								                                                                               yet.
								</td>
							</tr>
						@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</x-card>
