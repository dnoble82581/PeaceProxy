<?php

	use App\DTOs\Trigger\TriggerDTO;
	use App\Events\Trigger\TriggerUpdatedEvent;
	use App\Livewire\Forms\CreateTriggerForm;
 use App\Enums\General\ConfidenceScore;
 use App\Enums\Trigger\TriggerCategories;
 use App\Enums\Trigger\TriggerSensitivityLevels;
	use App\Models\Trigger;
	use App\Services\Trigger\TriggerUpdatingService;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Auth;

	new class extends Component {
		public CreateTriggerForm $form;
		public Trigger $trigger;

 	public function mount(Trigger $trigger):void
 	{
 		$this->trigger = $trigger;

 		// Reset the form before filling it with the new trigger data
 		$this->form->reset();
 		$this->form->fill($this->trigger);
		
 		// Commented out manual field setting for reference
 		// $this->form->tenant_id = $trigger->tenant_id;
 		// $this->form->subject_id = $trigger->subject_id;
 		// $this->form->created_by_id = $trigger->created_by_id;
 		// $this->form->negotiation_id = $trigger->negotiation_id;
 		// $this->form->title = $trigger->title;
 		// $this->form->description = $trigger->description;
 		// $this->form->category = $trigger->category;
 		// $this->form->sensitivity_level = $trigger->sensitivity_level;
 		// $this->form->source = $trigger->source;
 		// $this->form->confidence_score = $trigger->confidence_score;
 	}

 	public function updateTrigger():void
 	{
 		$validated = $this->form->validate();
 		$dto = TriggerDTO::fromArray($validated);
 		$trigger = app(TriggerUpdatingService::class)->updateTrigger($this->trigger->id, $dto);

 		// No need to emit event here as the service already does it
 		$this->dispatch('close-modal', $this->trigger->id);
 	}
	}

?>

<div>
	<form
			id="editTriggerForm"
			wire:submit.prevent="updateTrigger"
			class="space-y-6">

		<!-- Basic Information -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Trigger Information</h2>
			<p class="mb-4 text-sm text-gray-400">Edit the details about this trigger</p>

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

				<x-select.styled
						icon="chart-bar"
						label="Confidence Score"
						placeholder="Enter confidence score (0-1)"
						wire:model="form.confidence_score"
						:options="collect(App\Enums\General\ConfidenceScore::cases())->map(fn($score) => [
						'label' => $score->label(),
						'value' => $score->value])
						->toArray()" />

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
					primary>Update Trigger
			</x-button>
			<x-button
					type="button"
					color="secondary"
					x-on:click="$modalClose('edit-trigger-modal')">
				Cancel
			</x-button>
		</div>
	</form>
</div>
@push('scripts')
	<script>
		// Prevent form submission on Enter key press
		document.getElementById('editTriggerForm').addEventListener('keydown', function (event) {
			if (event.key === 'Enter') {
				event.preventDefault() // Stop the form from submitting
			}
		})
	</script>
@endpush