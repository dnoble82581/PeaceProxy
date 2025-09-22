<?php

	use App\DTOs\Warrant\WarrantDTO;
	use App\Livewire\Forms\CreateWarrantForm;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Models\Warrant;
	use App\Services\Negotiation\NegotiationFetchingService;
	use App\Services\Warrant\WarrantFetchingService;
	use App\Services\Warrant\WarrantUpdatingService;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;

	new #[Layout('layouts.negotiation')] class extends Component {
		public Warrant $warrant;
		public Negotiation $negotiation;
		public Subject $primarySubject;
		public CreateWarrantForm $form;
		public int $warrantId;

		public function mount($negotiationId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)->getNegotiationById($negotiationId);
			$this->primarySubject = $this->negotiation->primarySubject();
		}

		public function updateWarrant()
		{
			$validated = $this->form->validate();
			$dto = WarrantDTO::fromArray($validated);
			app(WarrantUpdatingService::class)->updateWarrant($dto, $this->warrantId);

			$this->dispatch('close-modal');
		}

		#[On('load-warrant')]
		public function loadWarrant($id):void
		{
			$this->warrantId = $id;
			$this->form->fill(app(WarrantFetchingService::class)->getWarrantById($id));
		}
	}

?>

<div
		class="bg-dark-700 rounded-lg"
		wire:ignore>
	<form
			wire:submit="updateWarrant"
			class="space-y-6 mt-6">
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white mb-4">Warrant Information</h2>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
				<!-- Warrant Type -->
				<div>
					<x-select.styled
							label="Warrant Type"
							wire:model.defer="form.type"
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
							wire:model.defer="form.status"
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
							wire:model.defer="form.jurisdiction"
							class="w-full" />
				</div>

				<!-- Court Name -->
				<div>
					<x-input
							placeholder="Court Name..."
							label="Court Name"
							id="court_name"
							type="text"
							wire:model.defer="form.court_name"
							class="w-full" />
				</div>

				<!-- Offense Description -->
				<div class="">
					<x-input
							label="Offense Description"
							placeholder="NCO, Theft..."
							id="offense_description"
							wire:model.defer="form.offense_description"
							class="w-full" />
				</div>

				<!-- Status Code -->
				<div>
					<x-input
							label="Status Code"
							placeholder="1234567890"
							id="status_code"
							type="text"
							wire:model.defer="form.status_code"
							class="w-full" />
				</div>

				<!-- Issued At -->
				<div>
					<x-date
							label="Issued At"
							id="issued_at"
							format="YYYY, MMMM, DD"
							wire:model.defer="form.issued_at"
							class="w-full" />
				</div>

				<!-- Expires At -->
				<div>
					<x-date
							format="YYYY, MMMM, DD"
							label="Expires At"
							id="expires_at"
							wire:model.defer="form.expires_at"
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
							wire:model.defer="form.bond_amount"
							class="w-full" />
				</div>

				<!-- Bond Type -->
				<div>
					<x-select.styled
							label="Bond Type"
							:request="route('enums.subject-bond-types')"
							id="bond_type"
							wire:model.defer="form.bond_type"
							class="w-full">
					</x-select.styled>
				</div>

			</div>
		</div>

		<!-- Navigation Buttons -->
		<div class="space-y-2">
			<x-button
					class="w-full"
					type="submit"
					primary>
				Update Warrant
			</x-button>
		</div>
	</form>
</div>