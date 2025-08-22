<?php

	use App\Events\Trigger\TriggerCreatedEvent;
	use App\Livewire\Forms\CreateTriggerForm;
	use App\Enums\Trigger\TriggerCategories;
	use App\Enums\Trigger\TriggerSensitivityLevels;
	use App\Models\Trigger;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Auth;

	new class extends Component {
		public CreateTriggerForm $form;
		public int $subjectId;
		public int $negotiationId;

		public function mount($subjectId, $negotiationId):void
		{
			$this->subjectId = $subjectId;
			$this->negotiationId = $negotiationId;
		}

		public function saveTrigger():void
		{
			$this->form->tenant_id = Auth::user()->tenant_id;
			$this->form->created_by_id = Auth::user()->id;
			$this->form->subject_id = $this->subjectId;
			$this->form->negotiation_id = $this->negotiationId;

			$validated = $this->form->validate();

			// Create the trigger
			$trigger = Trigger::create($validated);

			$this->dispatch('close-modal', $trigger->id);

			// Emit an event that the trigger was created
			if (class_exists(TriggerCreatedEvent::class)) {
				event(new TriggerCreatedEvent($trigger));
			}

			// Reset the form
			$this->form->reset();

			// Set default values again
			$this->form->tenant_id = Auth::user()->tenant_id;
			$this->form->created_by_id = Auth::user()->id;
		}
	}

?>

<div>
	<form
			id="createTriggerForm"
			wire:submit.prevent="saveTrigger"
			class="space-y-6">

		<!-- Basic Information -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Trigger Information</h2>
			<p class="mb-4 text-sm text-gray-400">Enter the details about this trigger</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-input
						icon="user"
						label="Title *"
						wire:model="form.title"
						placeholder="Enter trigger title" />

				<x-select.styled
						class="w-full"
						icon="tag"
						label="Category *"
						wire:model="form.category"
						:options="collect(App\Enums\Trigger\TriggerCategories::cases())->map(fn($category) => [
						'label' => $category->label(),
						'value' => $category->value,
					])->toArray()" />

				<x-select.styled
						class="w-full"
						icon="shield-exclamation"
						label="Sensitivity Level *"
						wire:model="form.sensitivity_level"
						:options="collect(App\Enums\Trigger\TriggerSensitivityLevels::cases())->map(fn($level) => [
						'label' => $level->label(),
						'value' => $level->value,
					])->toArray()" />

				<x-input
						icon="document-text"
						label="Source"
						wire:model="form.source"
						placeholder="Enter the source of this trigger" />

				<x-input
						type="number"
						step="0.01"
						min="0"
						max="1"
						icon="chart-bar"
						label="Confidence Score"
						wire:model="form.confidence_score"
						placeholder="Enter confidence score (0-1)" />

				<input
						type="hidden"
						wire:model="form.subject_id" />
				<input
						type="hidden"
						wire:model="form.negotiation_id" />
			</div>
		</div>

		<!-- Description -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Description</h2>
			<p class="mb-4 text-sm text-gray-400">Provide additional information about the trigger</p>

			<div class="grid grid-cols-1 gap-4">
				<x-textarea
						label="Description"
						wire:model="form.description"
						placeholder="Enter a description of the trigger"
						rows="3" />
			</div>
		</div>

		<!-- Submit Button -->
		<div class="flex items-center justify-end gap-4">
			<x-button
					type="submit"
					primary>Create Trigger
			</x-button>
			<x-button
					type="button"
					color="secondary"
					x-on:click="$modalClose('create-trigger-modal')">
				Cancel
			</x-button>
		</div>
	</form>
</div>
@push('scripts')
	<script>
		// Prevent form submission on Enter key press
		document.getElementById('createTriggerForm').addEventListener('keydown', function (event) {
			if (event.key === 'Enter') {
				event.preventDefault() // Stop the form from submitting
			}
		})
	</script>
@endpush