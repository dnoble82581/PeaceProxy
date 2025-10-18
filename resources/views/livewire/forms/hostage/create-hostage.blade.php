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

 new #[Layout('components.layouts.negotiation')] class extends Component {
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

			// Use the service to create the hostage and handle image uploads in one step
			$hostageCreationService = app(HostageCreationService::class);
			$hostage = $hostageCreationService->createHostage($hostageDTO, $this->images);

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

<div class="max-w-7xl mx-auto bg-dark-100 dark:bg-dark-800 p-8 mt-4 rounded-lg">
	<div class="px-4 sm:px-8 text-center space-y-3">
		<h1 class="text-2xl text-gray-400 font-semibold uppercase">Create New Hostage</h1>
		<p class="text-xs">Add a new hostage to the system</p>
	</div>

	<!-- Progress Bar -->
	<div class="mb-8 mt-4">
		<div class="flex justify-between mb-2">
			@for ($i = 1; $i <= $totalSteps; $i++)
				<button
						wire:click="goToStep({{ $i }})"
						class="flex flex-col items-center group">
					<div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= $i ? 'bg-primary-500' : 'bg-dark-400 dark:bg-dark-800' }} transition-colors duration-300">
						<span class="text-white">{{ $i }}</span>
					</div>
					<span class="text-xs mt-1 {{ $currentStep >= $i ? 'text-primary-400' : 'text-dark-500' }} group-hover:text-primary-300 transition-colors duration-300">
						@switch($i)
							@case(1)
								Basic Info
								@break
							@case(2)
								Status
									@break
							@case(3)
								Images
									@break
						@endswitch
					</span>
				</button>

				@if ($i < $totalSteps)
					<div class="flex-1 h-0.5 self-center {{ $currentStep > $i ? 'bg-primary-500' : 'bg-gray-700' }} transition-colors duration-300"></div>
				@endif
			@endfor
		</div>
	</div>

	<form
			id="createHostageForm"
			wire:submit.prevent="createHostage"
			class="space-y-6">

		<!-- Step 1: Basic Information -->
		<div
				class="mb-6"
				x-data="{}"
				x-show="$wire.currentStep == 1">
			<h2 class="text-lg font-semibold dark:text-white text-dark-800">Basic Information</h2>
			<p class="mb-4 text-sm text-dark-500">Enter the basic details about this hostage</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-input
						icon="user"
						label="Name *"
						wire:model="createHostageForm.name"
						placeholder="Enter hostage name" />

				<x-input
						icon="user"
						label="Age"
						wire:model="createHostageForm.age"
						placeholder="Enter age" />

				<x-select.styled
						searchable
						class="w-full"
						icon="user"
						label="Gender *"
						wire:model="createHostageForm.gender"
						:options="collect(App\Enums\General\Genders::cases())->map(fn($gender) => [
							'label' => $gender->label(),
							'value' => $gender->value,
						])->toArray()" />

				<x-select.styled
						searchable
						class="w-full"
						icon="user-group"
						label="Relation to Subject *"
						wire:model="createHostageForm.relation_to_subject"
						:options="collect(App\Enums\Hostage\HostageSubjectRelation::cases())->map(fn($relation) => [
							'label' => $relation->label(),
							'value' => $relation->value,
						])->toArray()" />
			</div>
		</div>

		<!-- Step 2: Status Information -->
		<div
				class="mb-6"
				x-data="{}"
				x-show="$wire.currentStep == 2">
			<h2 class="text-lg font-semibold text-white">Status Information</h2>
			<p class="mb-4 text-sm text-gray-400">Enter status information for this hostage</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-select.styled
						searchable
						class="w-full"
						icon="exclamation-triangle"
						label="Risk Level *"
						wire:model="createHostageForm.risk_level"
						:options="collect(App\Enums\General\RiskLevels::cases())->map(fn($risk) => [
							'label' => $risk->label(),
							'value' => $risk->value,
						])->toArray()" />

				<x-input
						icon="map-pin"
						label="Location"
						wire:model="createHostageForm.location"
						placeholder="Enter location" />

				<x-select.styled
						searchable
						class="w-full"
						icon="flag"
						label="Status *"
						wire:model="createHostageForm.status"
						:options="collect(App\Enums\Hostage\HostageStatus::cases())->map(fn($status) => [
							'label' => $status->label(),
							'value' => $status->value,
						])->toArray()" />

				<x-select.styled
						searchable
						class="w-full"
						icon="heart"
						label="Injury Status *"
						wire:model="createHostageForm.injury_status"
						:options="collect(App\Enums\Hostage\HostageInjuryStatus::cases())->map(fn($injury) => [
							'label' => $injury->label(),
							'value' => $injury->value,
						])->toArray()" />

				<div class="col-span-2">
					<x-tag
							icon="exclamation-triangle"
							label="Risk Factors"
							wire:model="createHostageForm.risk_factors"
							placeholder="Enter risk factors"
							hint="Enter known risk factors followed by enter." />
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
		<div
				class="mb-6"
				x-data="{}"
				x-show="$wire.currentStep == 3">
			<h2 class="text-lg font-semibold text-white">Hostage Images</h2>
			<p class="mb-4 text-sm text-gray-400">Upload images of the hostage</p>

			<div class="mb-6">
				<label class="block text-sm font-medium text-gray-300 mb-2">Upload Images</label>
				<div class="flex items-center justify-center w-full">
					<label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-600 rounded-lg cursor-pointer hover:bg-dark-600 transition-colors duration-300">
						<div class="flex flex-col items-center justify-center pt-7">
							<svg
									class="w-8 h-8 text-gray-400"
									fill="none"
									stroke="currentColor"
									viewBox="0 0 24 24"
									xmlns="http://www.w3.org/2000/svg">
								<path
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
							</svg>
							<p class="pt-1 text-sm text-gray-400">Drag and drop images or click to browse</p>
						</div>
						<input
								type="file"
								class="hidden"
								wire:model="images"
								multiple
								accept="image/*">
					</label>
				</div>
				<div
						wire:loading
						wire:target="images"
						class="mt-2 text-sm text-primary-400">
					Uploading...
				</div>
			</div>

			<!-- Image Preview -->
			@if(count($images) > 0)
				<div class="mt-4">
					<h3 class="text-md font-medium text-gray-300 mb-2">Hostage Images</h3>
					<div class="grid grid-cols-4 md:grid-cols-8 gap-4">
						@foreach($images as $index => $image)
							<div class="relative group">
								<div class="aspect-w-1 aspect-h-1 rounded-lg overflow-hidden bg-dark-600">
									<img
											src="{{ $image->temporaryUrl() }}"
											alt="Hostage image"
											class="w-full h-full object-cover">
								</div>
								<!-- Image info -->
								<div class="mt-1 text-xs text-gray-400">
									<p class="truncate">{{ $image->getClientOriginalName() }}</p>
									<p>{{ round($image->getSize() / 1024) }} KB</p>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			@else
				<div class="mt-4 text-center p-6 bg-dark-600 rounded-lg">
					<p class="text-gray-400">No images uploaded yet. Use the upload area above to add images.</p>
				</div>
			@endif
		</div>

		<!-- Navigation Buttons -->
		<div class="flex items-center justify-between gap-4 mt-8">
			<div>
				@if($currentStep > 1)
					<x-button
							sm
							wire:click="previousStep"
							color="secondary">
						Previous
					</x-button>
				@endif
			</div>

			<div class="flex items-center gap-4">
				<x-button
						sm
						wire:click="cancel"
						color="secondary">
					Cancel
				</x-button>

				@if($currentStep < $totalSteps)
					<x-button
							sm
							wire:click="nextStep"
							primary>
						Next
					</x-button>
				@else
					<x-button
							sm
							type="submit"
							primary>
						Create Hostage
					</x-button>
				@endif
			</div>
		</div>
	</form>
</div>

@push('scripts')
	<script>
		// Prevent form submission on Enter key press
		document.getElementById('createHostageForm').addEventListener('keydown', function (event) {
			if (event.key === 'Enter') {
				event.preventDefault() // Stop the form from submitting
			}
		})

		// Initialize Alpine.js components for step visibility
		document.addEventListener('alpine:init', () => {
			// Make sure all steps are properly shown/hidden based on current step
			window.addEventListener('livewire:initialized', () => {
				// Force Alpine to evaluate x-show directives
				document.querySelectorAll('[x-data]').forEach(el => {
					Alpine.initTree(el)
				})
			})

			// Re-evaluate x-show directives when step changes
			window.addEventListener('step-changed', () => {
				document.querySelectorAll('[x-data]').forEach(el => {
					Alpine.initTree(el)
				})
			})
		})
	</script>
@endpush