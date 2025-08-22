<?php

	use App\Livewire\Forms\CreateHostageForm;
	use App\Models\Negotiation;
	use App\Services\Image\ImageService;
	use App\Services\Hostage\HostageCreationService;
	use App\DTOs\Hostage\HostageDTO;
	use App\Enums\Hostage\HostageInjuryStatus;
	use App\Enums\Hostage\HostageStatus;
	use App\Enums\Hostage\HostageSubjectRelation;
	use App\Enums\General\RiskLevels;
	use App\Enums\General\Genders;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Illuminate\View\View;
	use Illuminate\Support\Facades\Auth;
	use Livewire\WithFileUploads;

	new #[Layout('layouts.negotiation')] class extends Component {
		use WithFileUploads;

		public ?Negotiation $negotiation = null;
		public CreateHostageForm $createHostageForm;
		public $currentStep = 1;
		public $totalSteps = 3;
		public $images = [];

		public function mount(?Negotiation $negotiation = null)
		{
			$this->negotiation = $negotiation;

			// Initialize the form with default values
			$this->createHostageForm->tenant_id = tenant()->id;
			$this->createHostageForm->negotiation_id = $negotiation?->id;
			$this->createHostageForm->created_by = Auth::id();
		}

		public function rendering(View $view):void
		{
			$view->layoutData(['negotiation' => $this->negotiation]);
		}

		public function nextStep()
		{
			if ($this->currentStep < $this->totalSteps) {
				$this->currentStep++;
				$this->dispatch('step-changed');
			}
		}

		public function previousStep()
		{
			if ($this->currentStep > 1) {
				$this->currentStep--;
				$this->dispatch('step-changed');
			}
		}

		public function goToStep($step)
		{
			if ($step >= 1 && $step <= $this->totalSteps) {
				$this->currentStep = $step;
				$this->dispatch('step-changed');
			}
		}

		public function createHostage()
		{
			// Validate the form
			$validated = $this->createHostageForm->validate();

			// Set timestamps
			$validated['created_at'] = now();
			$validated['updated_at'] = now();

			// Create a DTO from the validated data
			$hostageDTO = HostageDTO::fromArray($validated);

			// Use the service to create the hostage
			$hostageCreationService = app(HostageCreationService::class);
			$hostage = $hostageCreationService->createHostage($hostageDTO);

			// Handle image uploads using ImageService
			if (!empty($this->images)) {
				$imageService = app(ImageService::class);
				$imageService->uploadImagesForModel(
					$this->images,
					$hostage,
					'hostages',
					's3_public'
				);
			}

			// Redirect back to the negotiation page or dashboard
			if ($this->negotiation) {
				return redirect()->route('negotiation-noc', [
					'negotiation' => $this->negotiation->title,
					'tenantSubdomain' => tenant()->subdomain
				]);
			}

			return redirect()->route('dashboard.negotiations', tenant()->subdomain);
		}

		public function cancel()
		{
			// Redirect back to the negotiation page or dashboard
			if ($this->negotiation) {
				return redirect()->route('negotiation-noc', [
					'negotiation' => $this->negotiation->title,
					'tenantSubdomain' => tenant()->subdomain
				]);
			}

			return redirect()->route('dashboard.negotiations', tenant()->subdomain);
		}
	}

?>

<div class="py-12">
	<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
		<div class="bg-white dark:bg-dark-800 overflow-hidden shadow-sm sm:rounded-lg">
			<div class="p-6 text-gray-900 dark:text-gray-100">
				<h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
					Create New Hostage
				</h2>

				<div class="mt-6">
					<!-- Step Navigation -->
					<div class="mb-6">
						<div class="flex justify-between">
							<button
									wire:click="previousStep"
									class="px-4 py-2 bg-gray-200 dark:bg-dark-600 text-gray-800 dark:text-gray-200 rounded-md"
									{{ $currentStep === 1 ? 'disabled' : '' }}>
								Previous
							</button>

							<div class="flex space-x-2">
								@for ($i = 1; $i <= $totalSteps; $i++)
									<button
											wire:click="goToStep({{ $i }})"
											class="size-8 rounded-full {{ $currentStep === $i ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-dark-600 text-gray-800 dark:text-gray-200' }} flex items-center justify-center">
										{{ $i }}
									</button>
								@endfor
							</div>

							<button
									wire:click="nextStep"
									class="px-4 py-2 bg-gray-200 dark:bg-dark-600 text-gray-800 dark:text-gray-200 rounded-md"
									{{ $currentStep === $totalSteps ? 'disabled' : '' }}>
								Next
							</button>
						</div>
					</div>

					<!-- Step 1: Basic Information -->
					<div x-show="$wire.currentStep === 1">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<div>
								<x-input
										wire:model="createHostageForm.name"
										label="Name"
										id="name"
										class="block mt-1 w-full"
										type="text" />
							</div>

							<div>
								<x-input
										label="Age"
										wire:model="createHostageForm.age"
										id="age"
										class="block mt-1 w-full"
										type="text" />
							</div>

							<div>
								<x-select.styled
										label="Gender"
										wire:model="createHostageForm.gender"
										id="gender"
										class="block mt-1 w-full"
										:options="collect(App\Enums\General\Genders::cases())->map(fn($gender) => [
						                    'label' => $gender->label(),
						                    'value' => $gender->value,
					                        ])->toArray()" />


							</div>

							<div>
								<x-select.styled
										label="Relation to Subject"
										wire:model="createHostageForm.relation_to_subject"
										id="relation_to_subject"
										class="block mt-1 w-full"
										:options="collect(App\Enums\Hostage\HostageSubjectRelation::cases())->map(fn($relation) => [
						                    'label' => $relation->label(),
						                    'value' => $relation->value,
					                        ])->toArray()" />

							</div>
						</div>
					</div>

					<!-- Step 2: Status Information -->
					<div x-show="$wire.currentStep === 2">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<div>
								<x-select.styled
										label="Risk Level"
										wire:model="createHostageForm.risk_level"
										id="risk_level"
										class="block mt-1 w-full"
										:options="collect(App\Enums\General\RiskLevels::cases())->map(fn($risk) => [
						                    'label' => $risk->label(),
						                    'value' => $risk->value,
					                        ])->toArray()" />

							</div>

							<div>
								<x-input
										label="Location"
										wire:model="createHostageForm.location"
										id="location"
										class="block mt-1 w-full"
										type="text" />
							</div>

							<div>
								<x-select.styled
										label="Status"
										wire:model="createHostageForm.status"
										id="status"
										class="block mt-1 w-full"
										:options="collect(App\Enums\Hostage\HostageStatus::cases())->map(fn($status) => [
						                    'label' => $status->label(),
						                    'value' => $status->value,
					                        ])->toArray()" />

							</div>

							<div>
								<x-select.styled
										label="Injury Status"
										wire:model="createHostageForm.injury_status"
										id="injury_status"
										class="block mt-1 w-full"
										:options="collect(App\Enums\Hostage\HostageInjuryStatus::cases())->map(fn($injury) => [
						                    'label' => $injury->label(),
						                    'value' => $injury->value,
					                        ])->toArray()" />

							</div>

							<div>

								<x-tag
										label="Risk Factors"
										wire:model="createHostageForm.risk_factors"
										id="risk_factors"
										class="block mt-1 w-full"
										type="text" />
							</div>

							<div>

								<x-checkbox
										label="Primary Hostage"
										wire:model="createHostageForm.is_primary_hostage"
										id="is_primary_hostage"
										class="mt-1" />
							</div>
						</div>
					</div>

					<!-- Step 3: Images -->
					<div x-show="$wire.currentStep === 3">
						<div class="mb-6">
							<x-input
									label="Images"
									type="file"
									wire:model="images"
									multiple
									class="block mt-1 w-full"
									accept="image/*" />
							<p class="text-sm text-gray-500 mt-1">Upload one or more images of the hostage.</p>
						</div>

						<!-- Preview uploaded images -->
						@if(count($images) > 0)
							<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
								@foreach($images as $index => $image)
									<div class="relative">
										<img
												src="{{ $image->temporaryUrl() }}"
												alt="Hostage Image Preview"
												class="w-full h-32 object-cover rounded-lg">
									</div>
								@endforeach
							</div>
						@endif
					</div>

					<!-- Form Actions -->
					<div class="mt-6 flex justify-end space-x-4">
						<x-button
								wire:navigate.hover
								href="{{ $negotiation ? route('negotiation-noc', ['negotiation' => $negotiation->title, 'tenantSubdomain' => tenant()->subdomain]) : route('dashboard.negotiations', tenant()->subdomain) }}"
								color="secondary">Cancel
						</x-button>
						<x-button
								wire:click="createHostage"
								color="primary">Create Hostage
						</x-button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>