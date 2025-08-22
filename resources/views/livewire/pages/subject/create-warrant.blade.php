<?php

	use App\DTOs\Warrant\WarrantDTO;
	use App\Livewire\Forms\CreateWarrantForm;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Models\Warrant;
	use App\Services\Negotiation\NegotiationFetchingService;
	use App\Services\Warrant\WarrantCreationService;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('layouts.negotiation')] class extends Component {
		public Negotiation $negotiation;
		public Subject $primarySubject;
		public CreateWarrantForm $form;

		public function mount($negotiationId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)
				->getNegotiationById($negotiationId);

			$this->primarySubject = $this->negotiation->primarySubject();
			$this->form->tenant_id = $this->primarySubject->tenant_id;
			$this->form->subject_id = $this->primarySubject->id;
		}

		public function createWarrant()
		{
			$validated = $this->form->validate();

			$dto = WarrantDTO::fromArray($validated);

			app(WarrantCreationService::class)->createWarrant($dto);

			return $this->redirect(route('negotiation-noc',
				['tenantSubdomain' => tenant()->subdomain, 'negotiation' => $this->negotiation]));

		}
	}

?>
<div class="max-w-7xl mx-auto bg-dark-700 p-8 mt-4 rounded-lg">
	<div class="px-4 sm:px-8 text-center space-y-3">
		<h1 class="text-2xl text-gray-400 font-semibold uppercase">Create Warrant</h1>
		<p class="text-xs">Creating a warrant for: <span class="text-primary-400">{{ $primarySubject->name }}</span></p>
	</div>
	<form
			wire:submit="createWarrant"
			class="space-y-6 mt-6">
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white mb-4">Warrant Information</h2>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
			<!-- Warrant Type -->
			<div>
				<x-select.styled
						label="Warrant Type"
						wire:model="form.type"
						:request="route('enums.warrant-type')"
						class="w-full">
				</x-select.styled>
			</div>

			<!-- Status -->
			<div>
				<x-select.styled
						label="Status"
						:request="route('enums.warrant-status')"
						id="status"
						wire:model="form.status"
						class="w-full">
				</x-select.styled>
			</div>

			<!-- Jurisdiction -->
			<div>
				<x-input
						placeholder="Johnson County..."
						label="Jurisdiction"
						id="jurisdiction"
						type="text"
						wire:model="form.jurisdiction"
						class="w-full" />
			</div>

			<!-- Court Name -->
			<div>
				<x-input
						placeholder="Court Name..."
						label="Court Name"
						id="court_name"
						type="text"
						wire:model="form.court_name"
						class="w-full" />
			</div>

			<!-- Offense Description -->
			<div class="">
				<x-input
						label="Offense Description"
						placeholder="NCO, Theft..."
						id="offense_description"
						wire:model="form.offense_description"
						class="w-full" />
			</div>

			<!-- Status Code -->
			<div>
				<x-input
						label="Status Code"
						placeholder="1234567890"
						id="status_code"
						type="text"
						wire:model="form.status_code"
						class="w-full" />
			</div>

			<!-- Issued At -->
			<div>
				<x-date
						label="Issued At"
						id="issued_at"
						format="YYYY, MMMM, DD"
						wire:model="form.issued_at"
						class="w-full" />
			</div>

			<!-- Expires At -->
			<div>
				<x-date
						format="YYYY, MMMM, DD"
						label="Expires At"
						id="expires_at"
						wire:model="form.expires_at"
						class="w-full" />
			</div>

			<!-- Bond Amount -->
			<div>

				<x-currency
						symbol
						currency
						locale="en-US"
						label="Bond Amount"
						id="bond_amount"
						wire:model="form.bond_amount"
						class="w-full" />
			</div>

			<!-- Bond Type -->
			<div>
				<x-select.styled
						label="Bond Type"
						:request="route('enums.subject-bond-types')"
						id="bond_type"
						wire:model="form.bond_type"
						class="w-full">
				</x-select.styled>
			</div>

			</div>
		</div>
		
		<!-- Navigation Buttons -->
		<div class="flex items-center justify-between gap-4 mt-8">
			<div>
				<!-- Left side empty for consistency with edit-subject.blade.php -->
			</div>
			
			<div class="flex items-center gap-4">
				<x-button
						sm
						wire:navigate
						href="{{ route('negotiation-noc', ['tenantSubdomain' => tenant()->subdomain, 'negotiation' => $negotiation] ) }}"
						color="secondary">
					Cancel
				</x-button>
				<x-button
						sm
						type="submit"
						primary>
					Create Warrant
				</x-button>
			</div>
		</div>
	</form>
</div>
