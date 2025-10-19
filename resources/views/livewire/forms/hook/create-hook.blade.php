<?php

	use App\DTOs\Hook\HookDTO;
	use App\Events\Hook\HookCreatedEvent;
	use App\Livewire\Forms\CreateHookForm;
	use App\Enums\General\ConfidenceScore;
	use App\Enums\Hook\HookCategories;
	use App\Enums\Hook\HookSensitivityLevels;
	use App\Models\Hook;
	use App\Services\Hook\HookCreationService;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Auth;

	new class extends Component {
		public CreateHookForm $form;
		public int $subjectId;
		public int $negotiationId;


		public function mount($subjectId, $negotiationId):void
		{
			$this->subjectId = $subjectId;
			$this->negotiationId = $negotiationId;

		}

		public function saveHook():void
		{
			$this->form->tenant_id = Auth::user()->tenant_id;
			$this->form->created_by_id = Auth::user()->id;
			$this->form->subject_id = $this->subjectId;
			$this->form->negotiation_id = $this->negotiationId;

			$validated = $this->form->validate();

			// Create a DTO from the validated data
			$hookDTO = HookDTO::fromArray($validated);

			// Use the service to create the hook
			$hookService = app(HookCreationService::class);
			$hook = $hookService->createHook($hookDTO);

			$this->dispatch('close-modal', $hook->id);

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
			id="createHookForm"
			wire:submit.prevent="saveHook"
			class="space-y-6">

		<!-- Basic Information -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Hook Information</h2>
			<p class="mb-4 text-sm text-gray-400">Enter the details about this hook</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-input
						icon="user"
						label="Title *"
						wire:model="form.title"
						placeholder="Enter hook title" />

				<x-select.styled
						class="w-full"
						icon="tag"
						label="Category *"
						wire:model="form.category"
						:options="collect(App\Enums\Hook\HookCategories::cases())->map(fn($category) => [
						'label' => $category->label(),
						'value' => $category->value,
					])->toArray()" />

				<x-select.styled
						class="w-full"
						icon="shield-exclamation"
						label="Sensitivity Level *"
						wire:model="form.sensitivity_level"
						:options="collect(App\Enums\Hook\HookSensitivityLevels::cases())->map(fn($level) => [
						'label' => $level->label(),
						'value' => $level->value,
					])->toArray()" />

				<x-input
						icon="document-text"
						label="Source"
						wire:model="form.source"
						placeholder="Enter the source of this hook" />

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
			<p class="mb-4 text-sm text-gray-400">Provide additional information about the hook</p>

			<div class="grid grid-cols-1 gap-4">
				<x-textarea
						label="Description"
						wire:model="form.description"
						placeholder="Enter a description of the hook"
						rows="3" />
			</div>
		</div>

		<!-- Submit Button -->
		<div class="flex items-center justify-end gap-4">
			<x-button
					type="submit"
					primary>Create Hook
			</x-button>
			<x-button
					type="button"
					color="secondary"
					x-on:click="$modalClose('create-hook-modal')">
				Cancel
			</x-button>
		</div>
	</form>
</div>
@push('scripts')
	<script>
		// Prevent form submission on Enter key press (guard when form isn't present)
		(function(){
			const form = document.getElementById('createHookForm');
			if (!form) { return; }
			form.addEventListener('keydown', function (event) {
				if (event.key === 'Enter') {
					event.preventDefault(); // Stop the form from submitting
				}
			});
		})();
	</script>
@endpush
