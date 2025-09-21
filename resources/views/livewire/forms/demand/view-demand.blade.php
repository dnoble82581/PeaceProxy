<?php

	use App\DTOs\Demand\DemandDTO;
	use App\Livewire\Forms\CreateDemandForm;
	use App\Enums\Demand\DemandCategories;
	use App\Enums\Demand\DemandStatuses;
	use App\Enums\General\Channels;
	use App\Enums\General\RiskLevels;
	use App\Models\Demand;
	use App\Services\Demand\DemandUpdateService;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Auth;

	new class extends Component {
		public CreateDemandForm $form;
		public Demand $demand;

		public function mount(Demand $demand):void
		{
			$this->demand = $demand;

			// Reset the form before filling it with the new demand data
			$this->form->reset();
			$this->form->fill($this->demand);
		}

		public function updateDemand()
		{
			$this->demand->update([
				'updated_at' => now(),
				'updated_by' => authUser()->name,
				'responded_at' => now(),
				'resolved_at' => now(),
				'status' => DemandStatuses::approved,
			]);
		}

		public function cancel()
		{
			$this->dispatch('closeViewDemandModal');
		}
	}

?>

<div>
	{{ $errors }}
	<form
			id="editDemandForm"
			wire:submit.prevent="updateDemand"
			class="space-y-6">

		<!-- Basic Information -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Demand Information</h2>
			<p class="mb-4 text-sm text-gray-400">Edit the details about this demand</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-input
						disabled
						icon="document-text"
						label="Title *"
						wire:model="form.title"
						placeholder="Enter demand title" />

				<x-select.styled
						disabled
						class="w-full"
						icon="tag"
						label="Category *"
						wire:model="form.category"
						:options="collect(App\Enums\Demand\DemandCategories::cases())->map(fn($category) => [
						'label' => $category->label(),
						'value' => $category->value,
					])->toArray()" />

				<x-select.styled
						class="w-full"
						icon="status-online"
						label="Status *"
						wire:model="form.status"
						:options="collect(App\Enums\Demand\DemandStatuses::cases())->map(fn($status) => [
						'label' => $status->label(),
						'value' => $status->value,
					])->toArray()" />

				<x-select.styled
						class="w-full"
						icon="exclamation"
						label="Priority Level *"
						wire:model="form.priority_level"
						:options="collect(App\Enums\General\RiskLevels::cases())->map(fn($level) => [
						'label' => $level->label(),
						'value' => $level->value,
					])->toArray()" />

				<x-select.styled
						disabled
						class="w-full"
						icon="phone"
						label="Channel *"
						wire:model="form.channel"
						:options="collect(App\Enums\General\Channels::cases())->map(fn($channel) => [
						'label' => $channel->label(),
						'value' => $channel->value,
					])->toArray()" />

				<x-date
						disabled
						label="Deadline Date"
						wire:model="form.deadline_date"
						placeholder="Select deadline date" />

				<x-time
						disabled
						format="24"
						label="Deadline Time"
						wire:model="form.deadline_time"
						placeholder="Select deadline time" />

				<input
						type="hidden"
						wire:model="form.subject_id" />
				<input
						type="hidden"
						wire:model="form.negotiation_id" />
			</div>
		</div>

		<!-- Content -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Demand Content</h2>
			<p class="mb-4 text-sm text-gray-400">Edit the content of the demand</p>

			<div class="grid grid-cols-1 gap-4">
				<x-textarea
						disabled
						label="Content *"
						wire:model="form.content"
						placeholder="Enter the content of the demand"
						rows="3" />
			</div>
		</div>

		<!-- Submit Button -->
		<div class="flex items-center justify-end gap-4">
			<x-button
					sm
					primary
					type="submit"
					primary>Save
			</x-button>
			<x-button
					wire:click="cancel"
					type="button"
					sm
					type="button"
					color="secondary">
				Cancel
			</x-button>
		</div>
	</form>
</div>