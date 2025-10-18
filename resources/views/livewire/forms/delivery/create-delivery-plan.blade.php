<?php

	use App\DTOs\DeliveryPlan\DeliveryPlanDTO;
	use App\Enums\DeliveryPlan\Status as DeliveryPlanStatus;
	use App\Events\DeliveryPlan\DeliveryPlanCreatedEvent;
	use App\Livewire\Forms\DeliveryPlanForm;
	use App\Models\Demand;
	use App\Models\DeliveryPlan;
	use App\Services\DeliveryPlan\DeliveryPlanCreationService;
	use App\Services\Demand\DemandFetchingService;
	use Illuminate\Support\Facades\Auth;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;

	new #[Layout('components.layouts.negotiation')] class extends Component {
		public DeliveryPlanForm $form;
		public ?int $demandId = null;
		public ?Demand $demand = null;
		public ?int $negotiationId = null;
		public int $tenantId;
		public array $statusOptions = [];


		public function mount() {}

		#[On('load-demand')]
		public function loadDemand($demandId)
		{
			$this->demandId = $demandId;
			$this->demand = app(DemandFetchingService::class)->getDemandById($demandId);
		}

		public function fillRandom():void
		{
			$faker = fake();

			// Core fields
			$this->form->title = ucfirst($faker->words(3, true));
			$this->form->summary = $faker->paragraph();
			$this->form->category = $faker->randomElement(['logistics', 'transport', 'communication', 'security']);
			$this->form->status = $faker->randomElement([
				'pending', 'approved', 'in_progress', 'completed', 'cancelled'
			]);

			// Dates and times
			$startDate = now()->addDays(rand(0, 14));
			$this->form->scheduled_at = $startDate->format('Y-m-d');
			$startHour = rand(6, 18);
			$startMinute = collect([0, 15, 30, 45])->random();
			$endHour = min(23, $startHour + rand(1, 4));
			$endMinute = $startMinute;
			$this->form->window_starts_at = sprintf('%02d:%02d', $startHour, $startMinute);
			$this->form->window_ends_at = sprintf('%02d:%02d', $endHour, $endMinute);

			// Location
			$this->form->location_name = $faker->company().' - '.$faker->streetName();
			$this->form->location_address = $faker->streetAddress().', '.$faker->city().', '.$faker->stateAbbr().' '.$faker->postcode();

			// Pivot-like helpers
			$this->form->role = $faker->randomElement(['primary', 'backup', 'support', 'observer']);
			$this->form->notes = $faker->sentences(2, true);
		}


		public function save()
		{
			$this->form->negotiation_id = $this->demand->negotiation_id;
			$this->form->tenant_id = tenant()->id;
			$this->form->created_by = authUser()->id;
			$this->form->updated_by = authUser()->id;
			$this->form->created_at = now();
			$this->form->updated_at = now();


			$validated = $this->form->validate();
			// Ensure tenant_id is set properly
			$this->form->tenant_id = tenant()->id;

			$dto = DeliveryPlanDTO::fromArray($this->form->toArray());

			$deliveryPlan = app(DeliveryPlanCreationService::class)->createDeliveryPlan($dto);

			if ($this->demand) {
				$this->demand->deliveryPlans()->attach($deliveryPlan->id, [
					'role' => $this->form->role,        // Optional: use form data if available
					'notes' => $this->form->notes,      // Optional: use form data if available
				]);
			}

			$this->form->reset();

			$this->dispatch('close-modal');
			$this->dispatch('delivery-plan-created', $deliveryPlan->id);
		}
	}

?>

<div class="p-4">


	{{--	<div class="flex justify-end mb-3">--}}
	{{--		<x-button--}}
	{{--				wire:click="fillRandom"--}}
	{{--				color="orange"--}}
	{{--				icon="sparkles"--}}
	{{--				sm>Fill Random Data--}}
	{{--		</x-button>--}}
	{{--	</div>--}}
	<form
			wire:submit="save"
			class="space-y-4">
		<!-- Title -->
		<div>
			<x-input
					wire:model="form.title"
					label="Title *"
					id="title"
					class="block mt-1 w-full"
					type="text"
					hint="This is the title of the delivery plan."
					required />
		</div>
		<!-- Summary -->
		<div>
			<x-textarea
					wire:model="form.summary"
					label="Summary"
					id="summary"
					hint="This is a summary of the delivery plan."
					class="block mt-1 w-full" />
		</div>

		<!-- Category -->
		<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
			<x-input
					wire:model="form.category"
					label="Category"
					id="category"
					hint="Choose a category that best describes the delivery plan."
					class="block mt-1 w-full"
					type="text" />

			<x-select.styled
					class="w-full"
					label="Status"
					hint="What stage is this delivery plan in?"
					wire:model="form.status"
					:options="collect(DeliveryPlanStatus::cases())->map(fn($case) => [
						'label' => $case->label(),
						'value' => $case->value,
					])->toArray()" />
		</div>

		<!-- Window Starts At and Window Ends At -->
		<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

			<x-date
					wire:model="form.scheduled_at"
					label="Scheduled Date"
					hint="This is the day the delivery plan id scheduled to start?"
					id="scheduled_at"
					class="block mt-1 w-full" />

			<x-time
					wire:model="form.window_starts_at"
					label="Window Starts"
					id="window_starts_at"
					hint="This is the beginning window of the delivery plan."
					class="block mt-1 w-full"
					format="24" />

			<x-time
					wire:model="form.window_ends_at"
					label="Window Ends"
					id="window_ends_at"
					hint="This is the ending window of the delivery plan."
					class="block mt-1 w-full"
					format="24" />

		</div>

		<!-- Location Name and Address -->
		<div class="grid grid-cols-2 gap-4">
			<div>
				<x-input
						wire:model="form.location_name"
						label="Location Name"
						id="location_name"
						hint="Where is this going to take place take place."
						class="block mt-1 w-full"
						type="text" />
			</div>
			<div>
				<x-input
						wire:model="form.location_address"
						label="Location Address"
						hint="A specific address for the delivery plan if applicable."
						id="location_address"
						class="block mt-1 w-full"
						type="text" />
			</div>
		</div>

		<!-- Instructions -->
		{{--		<div>--}}
		{{--			<x-textarea--}}
		{{--					wire:model="form.instructions"--}}
		{{--					label="Instructions"--}}
		{{--					id="instructions"--}}
		{{--					class="block mt-1 w-full" />--}}
		{{--		</div>--}}

		<!-- Constraints -->
		{{--		<div>--}}
		{{--			<x-textarea--}}
		{{--					wire:model="form.constraints"--}}
		{{--					label="Constraints"--}}
		{{--					id="constraints"--}}
		{{--					class="block mt-1 w-full" />--}}
		{{--		</div>--}}


		{{--		<!-- Risk Assessment -->--}}
		{{--		<div>--}}
		{{--			<x-textarea--}}
		{{--					wire:model="form.risk_assessment"--}}
		{{--					label="Risk Assessment"--}}
		{{--					id="risk_assessment"--}}
		{{--					class="block mt-1 w-full" />--}}
		{{--		</div>--}}

		<!-- Role (for pivot table) -->
		<div>
			<x-input
					wire:model="form.role"
					label="Role"
					id="role"
					class="block mt-1 w-full"
					type="text" />
		</div>

		<!-- Notes (for pivot table) -->
		<div>
			<x-textarea
					wire:model="form.notes"
					label="Notes"
					id="notes"
					class="block mt-1 w-full" />
		</div>

		<div class="flex justify-end">
			<x-button
					type="submit"
					class="ml-3">
				Create Delivery Plan
			</x-button>
		</div>
	</form>
</div>
