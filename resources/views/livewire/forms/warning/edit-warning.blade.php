<?php

	use App\Livewire\Forms\CreateWarningForm;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Models\Warning;
	use App\Enums\General\RiskLevels;
	use App\Enums\Warning\WarningTypes;
	use App\Services\Negotiation\NegotiationFetchingService;
	use App\Services\Subject\SubjectFetchingService;
	use App\Services\Warning\WarningFetchingService;
	use App\Services\Warning\WarningUpdateService;
	use App\DTOs\Warning\WarningDTO;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('layouts.negotiation')] class extends Component {
		public Negotiation $negotiation;
		public Subject $subject;
		public Warning $warning;
		public CreateWarningForm $form;

		public function mount($negotiationId, $subjectId, $warningId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)
				->getNegotiationById($negotiationId);

			$this->subject = app(SubjectFetchingService::class)
				->fetchSubjectById($subjectId);

			$this->warning = app(WarningFetchingService::class)
				->fetchWarningById($warningId);

			// Fill the form with the warning data
			// Extract the value property from enum instances for risk_level and warning_type
			$this->form->fill([
				'tenant_id' => $this->warning->tenant_id,
				'subject_id' => $this->warning->subject_id,
				'created_by_id' => $this->warning->created_by_id,
				'risk_level' => $this->warning->risk_level->value,
				'warning_type' => $this->warning->warning_type->value,
				'warning' => $this->warning->warning,
			]);
		}

		public function updateWarning()
		{
			$validated = $this->form->validate();

			$warningDTO = new WarningDTO(
				$this->warning->id,
				$validated['subject_id'],
				$validated['tenant_id'],
				$validated['created_by_id'],
				$validated['risk_level'],
				$validated['warning_type'],
				$validated['warning'],
				$this->warning->created_at,
				now()
			);

			app(WarningUpdateService::class)->updateWarning($this->warning->id, $warningDTO);

			return $this->redirect(route('negotiation-noc',
				['tenantSubdomain' => tenant()->subdomain, 'negotiation' => $this->negotiation]));
		}
	}

?>
<div class="max-w-7xl mx-auto bg-dark-700 p-8 mt-4 rounded-lg">
	<div class="px-4 sm:px-8 text-center space-y-3">
		<h1 class="text-2xl text-gray-400 font-semibold uppercase">Edit Warning</h1>
		<p class="text-xs">Editing warning for:
			<span class="text-primary-400">{{ $subject->name }}</span></p>
	</div>
	<form
			wire:submit="updateWarning"
			class="space-y-6 mt-6">
		<h2 class="text-lg font-semibold text-white mb-4">Warning Information</h2>
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
			<!-- Warning Type -->
			<div>
				<x-select.styled
						label="Warning Type"
						wire:model="form.warning_type"
						:options="collect(App\Enums\Warning\WarningTypes::cases())->map(fn($type) => ['value' => $type->value, 'label' => $type->label()])->toArray()"
						class="w-full">
				</x-select.styled>
			</div>

			<!-- Risk Level -->
			<div>
				<x-select.styled
						label="Risk Level"
						wire:model="form.risk_level"
						:options="collect(App\Enums\General\RiskLevels::cases())->map(fn($level) => ['value' => $level->value, 'label' => $level->label()])->toArray()"
						class="w-full">
				</x-select.styled>
			</div>

			<!-- Warning Content -->
			<div class="col-span-2">
				<x-textarea
						label="Warning"
						placeholder="Enter detailed warning information here..."
						wire:model="form.warning"
						rows="5"
						class="w-full" />
			</div>
		</div>

		<!-- Navigation Buttons -->
		<div class="flex items-center justify-between gap-4 mt-8">
			<div>
				<!-- Left side empty for consistency -->
			</div>

			<div class="flex items-center gap-4">
				<x-button
						sm
						wire:navigate.hover
						href="{{ route('negotiation-noc', ['tenantSubdomain' => tenant()->subdomain, 'negotiation' => $negotiation]) }}"
						color="secondary">
					Cancel
				</x-button>
				<x-button
						sm
						type="submit"
						primary>
					Update Warning
				</x-button>
			</div>
		</div>
	</form>
</div>