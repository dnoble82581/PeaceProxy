<?php

	use App\Livewire\Forms\CreateContactPointForm;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Models\ContactPoint;
	use App\Services\Negotiation\NegotiationFetchingService;
	use App\Services\Subject\SubjectFetchingService;
	use App\Services\ContactPoint\ContactPointFetchingService;
	use App\Services\ContactPoint\ContactPointUpdateService;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('layouts.negotiation')] class extends Component {
		public Negotiation $negotiation;
		public Subject $subject;
		public ContactPoint $contactPoint;
		public CreateContactPointForm $form;

		public function mount($negotiationId, $subjectId, $contactPointId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)
				->getNegotiationById($negotiationId);

			$this->subject = app(SubjectFetchingService::class)
				->fetchSubjectById($subjectId);

			$this->contactPoint = app(ContactPointFetchingService::class)
				->getContactPointById($contactPointId);

			// Fill the form with the contact point data
			$this->form->fill([
				'tenant_id' => $this->contactPoint->tenant_id,
				'subject_id' => $this->contactPoint->subject_id,
				'kind' => $this->contactPoint->kind,
				'label' => $this->contactPoint->label,
				'is_primary' => $this->contactPoint->is_primary,
				'is_verified' => $this->contactPoint->is_verified,
				'priority' => $this->contactPoint->priority,
			]);

			// Fill the specific contact point type data
			if ($this->contactPoint->kind === 'email' && $this->contactPoint->email) {
				$this->form->email = $this->contactPoint->email->email;
			} elseif ($this->contactPoint->kind === 'phone' && $this->contactPoint->phone) {
				$this->form->e164 = $this->contactPoint->phone->e164;
				$this->form->ext = $this->contactPoint->phone->ext;
				$this->form->phone_country_iso = $this->contactPoint->phone->country_iso;
			} elseif ($this->contactPoint->kind === 'address' && $this->contactPoint->address) {
				$this->form->address1 = $this->contactPoint->address->address1;
				$this->form->address2 = $this->contactPoint->address->address2;
				$this->form->city = $this->contactPoint->address->city;
				$this->form->region = $this->contactPoint->address->region;
				$this->form->postal_code = $this->contactPoint->address->postal_code;
				$this->form->address_country_iso = $this->contactPoint->address->country_iso;
				$this->form->latitude = $this->contactPoint->address->latitude;
				$this->form->longitude = $this->contactPoint->address->longitude;
			}
		}

		public function updateContactPoint()
		{
			$validated = $this->form->validate();

			app(ContactPointUpdateService::class)->updateContactPoint($this->contactPoint->id, $validated);

			return $this->redirect(route('negotiation-noc',
				['tenantSubdomain' => tenant()->subdomain, 'negotiation' => $this->negotiation]));
		}
	}

?>
<div class="max-w-7xl mx-auto bg-dark-700 p-8 mt-4 rounded-lg">
	<div class="px-4 sm:px-8 text-center space-y-3">
		<h1 class="text-2xl text-gray-400 font-semibold uppercase">Edit Contact Point</h1>
		<p class="text-xs">Editing contact point for:
			<span class="text-primary-400">{{ $subject->name }}</span></p>
	</div>
	<form
			wire:submit="updateContactPoint"
			class="space-y-6 mt-6">
		<h2 class="text-lg font-semibold text-white mb-4">Contact Information</h2>
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
			<!-- Contact Type -->
			<div>
				<x-select.styled
						label="Contact Type"
						wire:model="form.kind"
						:options="[
                            ['value' => 'email', 'label' => 'Email'],
                            ['value' => 'phone', 'label' => 'Phone'],
                            ['value' => 'address', 'label' => 'Address'],
                        ]"
						class="w-full">
				</x-select.styled>
			</div>

			<!-- Label -->
			<div>
				<x-select.styled
						label="Label"
						wire:model="form.label"
						:options="[
                            ['value' => 'home', 'label' => 'Home'],
                            ['value' => 'work', 'label' => 'Work'],
                            ['value' => 'billing', 'label' => 'Billing'],
                            ['value' => 'other', 'label' => 'Other'],
                        ]"
						class="w-full">
				</x-select.styled>
			</div>

			<!-- Is Primary -->
			<div>
				<x-checkbox
						label="Primary Contact"
						wire:model="form.is_primary"
						class="" />
			</div>

			<!-- Is Verified -->
			<div>
				<x-checkbox
						label="Verified"
						wire:model="form.is_verified"
						class="" />
			</div>

			<!-- Email fields (shown only when kind is email) -->
			<div
					class="col-span-2"
					x-show="$wire.form.kind === 'email'">
				<x-input
						label="Email Address"
						placeholder="example@example.com"
						wire:model="form.email"
						class="w-full" />
			</div>

			<!-- Phone fields (shown only when kind is phone) -->
			<div
					class="col-span-2 space-y-4"
					x-show="$wire.form.kind === 'phone'">
				<x-input
						label="Phone Number (E.164 format)"
						placeholder="+14155550123"
						wire:model="form.e164"
						class="w-full" />

				<div class="grid grid-cols-2 gap-4">
					<x-input
							label="Extension"
							placeholder="123"
							wire:model="form.ext"
							class="w-full" />

					<x-input
							label="Country ISO"
							placeholder="US"
							wire:model="form.phone_country_iso"
							class="w-full" />
				</div>
			</div>

			<!-- Address fields (shown only when kind is address) -->
			<div
					class="col-span-2 space-y-4"
					x-show="$wire.form.kind === 'address'">
				<x-input
						label="Address Line 1"
						placeholder="123 Main St"
						wire:model="form.address1"
						class="w-full" />

				<x-input
						label="Address Line 2"
						placeholder="Apt 4B"
						wire:model="form.address2"
						class="w-full" />

				<div class="grid grid-cols-2 gap-4">
					<x-input
							label="City"
							placeholder="San Francisco"
							wire:model="form.city"
							class="w-full" />

					<x-input
							label="Region/State"
							placeholder="CA"
							wire:model="form.region"
							class="w-full" />
				</div>

				<div class="grid grid-cols-2 gap-4">
					<x-input
							label="Postal Code"
							placeholder="94103"
							wire:model="form.postal_code"
							class="w-full" />

					<x-input
							label="Country ISO"
							placeholder="US"
							wire:model="form.address_country_iso"
							class="w-full" />
				</div>

				<div class="grid grid-cols-2 gap-4">
					<x-input
							label="Latitude"
							placeholder="37.7749"
							wire:model="form.latitude"
							class="w-full" />

					<x-input
							label="Longitude"
							placeholder="-122.4194"
							wire:model="form.longitude"
							class="w-full" />
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
						wire:navigate.hover
						href="{{ route('negotiation-noc', ['tenantSubdomain' => tenant()->subdomain, 'negotiation' => $negotiation]) }}"
						color="secondary">
					Cancel
				</x-button>
				<x-button
						sm
						type="submit"
						primary>
					Update Contact Point
				</x-button>
			</div>
		</div>
	</form>
</div>