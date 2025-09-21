<?php

	use App\DTOs\Warning\WarningDTO;
	use App\Livewire\Forms\CreateWarningForm;
	use App\Services\Warning\WarningCreationService;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Auth;

	new class extends Component {
		public CreateWarningForm $form;
		public int $subjectId;
		public ?int $negotiationId = null; // kept for parity with create-hook (not used by warnings)

		public function mount($subjectId, $negotiationId = null):void
		{
			$this->subjectId = (int) $subjectId;
			$this->negotiationId = $negotiationId? (int) $negotiationId : null;
		}

		public function saveWarning():void
		{
			$this->form->tenant_id = Auth::user()->tenant_id;

			$this->form->created_by_id = Auth::user()->id;
			$this->form->subject_id = $this->subjectId;
			$this->form->created_at = now();
			$this->form->updated_at = now();

			$validated = $this->form->validate();

			$warningDTO = WarningDTO::fromArray($validated);
			$service = app(WarningCreationService::class);
			$warning = $service->createWarning($warningDTO);

			// close modal if listening
			$this->dispatch('close-modal', $warning->id);
			$this->dispatch('warning-created', id: $warning->id);

			$this->form->reset();
			$this->form->tenant_id = Auth::user()->tenant_id;
			$this->form->created_by_id = Auth::user()->id;

			event(new \App\Events\Warning\WarningCreatedEvent($this->subjectId));

			$this->dispatch('close-create-warning-modal');
		}
	};

?>

<div>
	<form
			id="createWarningForm"
			wire:submit.prevent="saveWarning"
			class="space-y-6">
		<!-- Basic Information -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Warning Information</h2>
			<p class="mb-4 text-sm text-gray-400">Enter the details about this warning</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-select.styled
						class="w-full"
						icon="tag"
						label="Type *"
						wire:model="form.warning_type"
						:options="collect(App\Enums\Warning\WarningTypes::cases())->map(fn($t) => ['label' => $t->label(), 'value' => $t->value])->toArray()" />

				<x-select.styled
						class="w-full"
						icon="shield-exclamation"
						label="Risk Level *"
						wire:model="form.risk_level"
						:options="collect(App\Enums\General\RiskLevels::cases())->map(fn($r) => ['label' => $r->label(), 'value' => $r->value])->toArray()" />

				<input
						type="hidden"
						wire:model="form.subject_id" />
			</div>
		</div>

		<!-- Description -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Details</h2>
			<p class="mb-4 text-sm text-gray-400">Provide additional information about the warning</p>

			<div class="grid grid-cols-1 gap-4">
				<x-textarea
						label="Warning *"
						wire:model="form.warning"
						placeholder="Describe the warning"
						rows="3" />
			</div>
		</div>

		<!-- Submit Button -->
		<div class="flex items-center justify-end gap-4">
			<x-button
					type="submit"
					primary>
				Create Warning
			</x-button>
			<x-button
					type="button"
					color="secondary"
					x-on:click="$modalClose('CreateWarningModal')">
				Cancel
			</x-button>
		</div>
	</form>
</div>
