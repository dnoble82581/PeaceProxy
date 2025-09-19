<?php

	use App\Events\DeliveryPlan\DeliveryPlanUpdatedEvent;
	use App\Models\DeliveryPlan;
	use App\DTOs\DeliveryPlan\DeliveryPlanDTO;
	use Illuminate\Support\Facades\Auth;

	new class extends \Livewire\Volt\Component {
		public \App\Livewire\Forms\DeliveryPlanForm $form;
		public ?DeliveryPlan $deliveryPlan = null;
		public ?int $deliveryPlanId = null;
		public ?int $negotiationId = null;
		public int $tenantId;

		public function mount($deliveryPlanId)
		{
			// Ensure deliveryPlanId is properly assigned
			$this->deliveryPlanId = $deliveryPlanId;

			if ($this->deliveryPlanId) {
				$this->deliveryPlan = DeliveryPlan::findOrFail($this->deliveryPlanId);

				// Check if the user is authorized to update this delivery plan
				if (!Auth::user()->can('update', $this->deliveryPlan)) {
					session()->flash('error', 'You are not authorized to edit this delivery plan.');
					$this->dispatch('close-modal');
					return;
				}

				// Set negotiation_id from delivery plan
				$this->negotiationId = $this->deliveryPlan->negotiation_id;

				// Map delivery plan data to form
				$this->mapDeliveryPlanToForm();
			} else {
				session()->flash('error', 'Delivery plan not found.');
				$this->dispatch('close-modal');
			}
		}

		protected function mapDeliveryPlanToForm()
		{
			// Convert the model to an array
			$deliveryPlanArray = $this->deliveryPlan->toArray();

			// Iterate over the delivery form properties and assign values from the delivery plan
			foreach (get_object_vars($this->form) as $key => $value) {
				if (array_key_exists($key, $deliveryPlanArray)) {
					$this->form->$key = $deliveryPlanArray[$key];
				}
			}

			// Ensure updated_by is set to current user
			$this->form->updated_by = Auth::id();
		}

		public function save()
		{
			// Check if the user is authorized to update this delivery plan
			if (!Auth::user()->can('update', $this->deliveryPlan)) {
				session()->flash('error', 'You are not authorized to update this delivery plan.');
				return;
			}

			// Set the current user as the updater
			$this->form->updated_by = Auth::id();

			// Create DTO from form data
			$dto = DeliveryPlanDTO::fromArray($this->form->toArray());

			// Update the delivery plan
			$updatedDeliveryPlan = app(\App\Services\DeliveryPlan\DeliveryPlanUpdateService::class)
				->updateDeliveryPlan($dto, $this->deliveryPlanId);

			// Close the modal and dispatch event
			$this->dispatch('close-modal');
			$this->dispatch('delivery-plan-updated', $updatedDeliveryPlan->id);
		}
	}

?>

<div class="p-4">
	<form
			wire:submit="save"
			class="space-y-4">
		<!-- Title -->
		<div>
			<x-input
					wire:model="form.title"
					label="Title"
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
					:options="[
                    ['label' => 'Pending', 'value' => 'pending'],
                    ['label' => 'Approved', 'value' => 'approved'],
                    ['label' => 'In Progress', 'value' => 'in_progress'],
                    ['label' => 'Completed', 'value' => 'completed'],
                    ['label' => 'Cancelled', 'value' => 'cancelled']
                ]" />
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

		<!-- Contingencies -->
		{{--		<div>--}}
		{{--			<x-textarea--}}
		{{--					wire:model="form.contingencies"--}}
		{{--					label="Contingencies"--}}
		{{--					id="contingencies"--}}
		{{--					class="block mt-1 w-full" />--}}
		{{--		</div>--}}

		<!-- Risk Assessment -->
		{{--		<div>--}}
		{{--			<x-textarea--}}
		{{--					wire:model="form.risk_assessment"--}}
		{{--					label="Risk Assessment"--}}
		{{--					id="risk_assessment"--}}
		{{--					class="block mt-1 w-full" />--}}
		{{--		</div>--}}

		<!-- Role (for pivot table) -->
		{{--		<div>--}}
		{{--			<x-input--}}
		{{--					wire:model="form.role"--}}
		{{--					label="Role"--}}
		{{--					id="role"--}}
		{{--					class="block mt-1 w-full"--}}
		{{--					type="text" />--}}
		{{--		</div>--}}

		{{--		<!-- Notes (for pivot table) -->--}}
		{{--		<div>--}}
		{{--			<x-textarea--}}
		{{--					wire:model="form.notes"--}}
		{{--					label="Notes"--}}
		{{--					id="notes"--}}
		{{--					class="block mt-1 w-full" />--}}
		{{--		</div>--}}

		<div class="flex justify-end">
			<x-button
					type="submit"
					class="ml-3">
				Update Delivery Plan
			</x-button>
		</div>
	</form>
</div>