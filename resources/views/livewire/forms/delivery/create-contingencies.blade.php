<?php

	use App\Enums\DeliveryPlan\ContingencyStatus;
	use App\Models\DeliveryPlan;
	use Illuminate\Support\Str;

	new class extends \Livewire\Volt\Component {
		public ?int $deliveryPlanId = null;
		/** @var array<string,mixed> */
		public array $newContingency = [];
		/** @var array<int,array{label:string,value:string}> */
		public array $contingencyStatusOptions = [];

		public function mount(?int $deliveryPlanId = null): void
		{
			$this->deliveryPlanId = $deliveryPlanId;
			$this->buildStatusOptions();
			$this->resetNewContingency();
		}

		protected function buildStatusOptions(): void
		{
			$this->contingencyStatusOptions = collect(ContingencyStatus::cases())
				->map(fn ($c) => ['label' => $c->label(), 'value' => $c->value])
				->values()
				->all();
		}

		protected function resetNewContingency(): void
		{
			$this->newContingency = [
				'id' => (string) Str::uuid(),
				'title' => '',
				'triggers' => '',
				'actions' => '',
				'_resources_input' => '',
				'resources' => [],
				'comms' => '',
				'criteria' => '',
				'notes' => '',
				'status' => ContingencyStatus::draft->value,
			];
		}

		public function saveContingency(): void
		{
			$title = trim((string) ($this->newContingency['title'] ?? ''));
			if ($title === '') {
				session()->flash('error', 'Title is required for a contingency.');
				return;
			}

			$plan = $this->deliveryPlanId ? DeliveryPlan::find($this->deliveryPlanId) : null;
			if (!$plan) {
				session()->flash('error', 'Delivery plan not found.');
				return;
			}

			// Normalize resources from comma list
			$list = (string) ($this->newContingency['_resources_input'] ?? '');
			$this->newContingency['resources'] = collect(explode(',', $list))
				->map(fn ($s) => trim($s))
				->filter()
				->values()
				->all();
			unset($this->newContingency['_resources_input']);

			$existing = is_array($plan->contingencies) ? $plan->contingencies : [];
			$existing[] = $this->newContingency;
			$plan->contingencies = array_values($existing);
			$plan->save();

			// Close the slide and notify parent if needed
			$this->dispatch('close');
			$this->dispatch('contingency-created', planId: $plan->id);

			// Reset local form for potential next entry
			$this->resetNewContingency();
		}
	};

?>

<div class="space-y-3">
	<x-input
			label="Title *"
			wire:model.live="newContingency.title" />
	<div class="grid sm:grid-cols-2 gap-3">
		<x-textarea
				label="Triggers"
				wire:model.live="newContingency.triggers"
				rows="2" />
		<x-textarea
				label="Actions"
				wire:model.live="newContingency.actions"
				rows="2" />
	</div>
	<x-input
			label="Resources (comma separated)"
			wire:model.live="newContingency._resources_input"
			placeholder="Team 2, Truck 14" />
	<div class="grid sm:grid-cols-3 gap-3">
		<x-input
				label="Comms"
				wire:model.live="newContingency.comms" />
		<x-input
				label="Criteria"
				wire:model.live="newContingency.criteria" />
		<x-select.styled
				label="Status"
				wire:model.live="newContingency.status"
				:options="$contingencyStatusOptions" />
	</div>
	<x-textarea
			label="Notes"
			wire:model.live="newContingency.notes"
			rows="2" />

	<div class="flex justify-end gap-2 pt-2">
		<x-button
				variant="secondary"
				x-on:click="$dispatch('close')">Cancel
		</x-button>
		<x-button wire:click="saveContingency">Save</x-button>
	</div>
</div>
