<?php

	use App\Livewire\Forms\CreateWarningForm;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Enums\General\RiskLevels;
	use App\Enums\Warning\WarningTypes;
	use App\Services\Negotiation\NegotiationFetchingService;
	use App\Services\Subject\SubjectFetchingService;
	use App\Services\Warning\WarningCreationService;
	use App\DTOs\Warning\WarningDTO;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('layouts.negotiation')] class extends Component {
		public Negotiation $negotiation;
		public Subject $subject;
		public CreateWarningForm $form;

		public function mount($negotiationId, $subjectId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)
				->getNegotiationById($negotiationId);

			$this->subject = app(SubjectFetchingService::class)
				->fetchSubjectById($subjectId);

			$this->form->tenant_id = auth()->user()->tenant_id;
			$this->form->subject_id = $this->subject->id;
			$this->form->created_by_id = auth()->id();
		}

		public function createWarning()
		{
			$validated = $this->form->validate();

			$warningDTO = new WarningDTO(
				null,
				$validated['subject_id'],
				$validated['tenant_id'],
				$validated['created_by_id'],
				$validated['risk_level'],
				$validated['warning_type'],
				$validated['warning']
			);

			app(WarningCreationService::class)->createWarning($warningDTO);

			return $this->redirect(route('negotiation-noc',
				['tenantSubdomain' => tenant()->subdomain, 'negotiation' => $this->negotiation]));
		}
	}

?>
<div class="max-w-7xl mx-auto bg-dark-700 p-8 mt-4 rounded-lg">
	<div class="px-4 sm:px-8 text-center space-y-3">
		<h1 class="text-2xl text-gray-400 font-semibold uppercase">Create Warning</h1>
		<p class="text-xs">Creating a warning for:
			<span class="text-primary-400">{{ $subject->name }}</span></p>
	</div>
	<form
			wire:submit="createWarning"
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
					Create Warning
				</x-button>
			</div>
		</div>
	</form>
</div>