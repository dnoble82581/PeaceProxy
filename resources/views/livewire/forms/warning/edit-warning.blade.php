<?php

	use App\DTOs\Warning\WarningDTO;
	use App\Livewire\Forms\CreateWarningForm;

	// reuse for validation
	use App\Models\Warning;
	use App\Services\Warning\WarningUpdatingService;
	use Illuminate\Support\Facades\Auth;
	use Livewire\Volt\Component;

	new class extends Component {
		public CreateWarningForm $form;
		public int $warningId;
		public Warning $warning;

		public function mount($warningId):void
		{
			$this->warningId = (int) $warningId;
			$warning = Warning::findOrFail($this->warningId);
			$this->warning = $warning;


			// Pre-fill form fields
			$this->form->subject_id = $warning->subject_id;
			$this->form->tenant_id = Auth::user()->tenant_id; // keep tenant scoped to current
			$this->form->created_by_id = $warning->created_by_id ?? Auth::user()->id;
			$this->form->risk_level = $warning->risk_level?->value ?? 'low';
			$this->form->warning_type = $warning->warning_type?->value ?? 'other';
			$this->form->warning = $warning->warning;
		}

		public function updateWarning():void
		{
			// Ensure tenant/creator preserved
			$this->form->tenant_id = Auth::user()->tenant_id;
			if (empty($this->form->created_by_id)) {
				$this->form->created_by_id = Auth::user()->id;
			}

			$validated = $this->form->validate();
			$dto = WarningDTO::fromArray($validated);
			$service = app(WarningUpdatingService::class);
			$service->updateWarning($dto, $this->warningId);

			event(new \App\Events\Warning\WarningUpdatedEvent($this->warning->subject_id, $this->warningId));
			$this->dispatch('close-edit-warning-modal');
		}

		public function cancel()
		{
			$this->dispatch('close-modals');
		}
	};

?>

<div>
	<form
			id="editWarningForm"
			wire:submit.prevent="updateWarning"
			class="space-y-6">
		<!-- Basic Information -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold dark:text-white text-dark-800">Edit Warning</h2>
			<p class="mb-4 text-sm text-gray-400">Update the details for this warning</p>

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
			</div>
		</div>

		<!-- Description -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold dark:text-white text-dark-800">Details</h2>
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
		<div class="space-y-2">
			<x-button
					class="w-full"
					type="submit"
					primary>
				Update Warning
			</x-button>
			<x-button
					class="w-full"
					color="slate"
					wire:click="cancel"
			>Cancel
			</x-button>
		</div>
	</form>
</div>
@push('scripts')
	<script>
		document.getElementById('editWarningForm')?.addEventListener('keydown', function (event) {
			if (event.key === 'Enter') {
				event.preventDefault()
			}
		})
	</script>
@endpush
