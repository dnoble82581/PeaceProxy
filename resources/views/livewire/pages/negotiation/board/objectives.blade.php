<?php

	use App\Enums\Objective\Priority;
	use App\Enums\Objective\Status;
	use App\Models\Objective;
	use App\Models\User;
	use Illuminate\Support\Collection;
	use Livewire\Attributes\Computed;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;

	new class extends Component {
		public ?int $negotiation_id = null;
		public ?string $objective = null;
		public string $priority = Priority::medium->value;
		public ?int $editingId = null;
		public bool $showForm = false;

		public function mount(int $negotiationId = null):void
		{
			$this->negotiation_id = $negotiationId;
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
				$objective = Objective::findOrFail($this->editingId);
				$objective->update([
					'objective' => $this->objective,
					'priority' => $this->priority,
				]);

				$this->dispatch('notify', [
					'message' => 'Objective updated successfully!',
					'type' => 'success',
				]);
			} else {
				Objective::create([
					'tenant_id' => authUser()->tenant_id,
					'negotiation_id' => $this->negotiation_id,
					'created_by_id' => auth()->id(),
					'objective' => $this->objective,
					'priority' => $this->priority,
					'status' => Status::pending->value,
				]);

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
			$objective = Objective::findOrFail($id);
			$objective->delete();

			$this->dispatch('notify', [
				'message' => 'Objective deleted successfully!',
				'type' => 'success',
			]);
		}

		public function complete(int $id):void
		{
			$objective = Objective::findOrFail($id);
			$objective->update([
				'status' => Status::completed->value,
				'completed_at' => now(),
				'completed_by_id' => auth()->id(),
			]);

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
				size="sm">
			{{ $showForm ? 'Cancel' : 'Add Objective' }}
		</x-button>
	</div>

	@if($showForm)
		<div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6">
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
								<time datetime="{{ $objective->created_at->toIso8601String() }}">{{ $objective->created_at->format('F j, Y') }}</time>
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
									<time datetime="{{ $objective->completed_at->toIso8601String() }}">{{ $objective->completed_at->format('F j, Y') }}</time>
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
									wire:click="edit({{ $objective->id }})"
									icon="pencil-square">Edit
							</x-dropdown.items>
							@if($objective->status !== 'completed')
								<x-dropdown.items
										wire:click="complete({{ $objective->id }})"
										icon="check">Complete
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