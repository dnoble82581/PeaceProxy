<?php

	use App\Enums\Activity\ActivityType;
	use App\Livewire\Forms\ActivityForm;
	use App\Models\Negotiation;
	use App\Services\Activity\ActivityCreationService;
	use App\Services\Negotiation\NegotiationFetchingService;
	use Illuminate\Support\Collection;
	use Livewire\Volt\Component;

	new class extends Component {
		public int $negotiationId;
		public Negotiation $negotiation;
		public Collection $activities;
		public int $subjectId;
		public ActivityForm $form;

		public function mount(int $negotiationId, int $subjectId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)->getNegotiationById($negotiationId);
			$this->loadActivities();
		}

		public function loadActivities()
		{
			$this->activities = $this->negotiation->activities()
				->orderBy('entered_at', 'desc')
				->get();
			$this->negotiation->load('activities');
		}

		public function saveTimeline()
		{
			$this->form->tenant_id = tenant()->id;
			$this->form->subject_id = $this->subjectId;
			$this->form->negotiation_id = $this->negotiationId;
			$this->form->user_id = authUser()->id;
			$validated = $this->form->validate();

			$dto = new App\DTOs\Activity\ActivityDTO(
				tenant_id: $validated['tenant_id'],
				negotiation_id: $validated['negotiation_id'],
				user_id: $validated['user_id'],
				subject_id: $validated['subject_id'],
				type: isset($validated['type'])? ActivityType::tryFrom($validated['type']) : null,
				activity: $validated['activity'],
				is_flagged: false,
				entered_at: now(),
				created_at: now(),
				updated_at: now()
			);

			$this->form->reset();

			app(ActivityCreationService::class)->createActivity($dto);
			$this->loadActivities();
		}
	}

?>

<div>
	<form wire:submit.prevent="saveTimeline">
		<div class="flex gap-4 items-center px-4">
			<div class="flex gap-4 items-center">
				<x-button
						text="Save"
						type="submit" />
				<div class="w-48">
					<x-select.styled
							wire:model="form.type"
							:options="collect(ActivityType::cases())->map(fn($type) => [
								'label' => $type->label(),
								'value' => $type->value,
						])">
					</x-select.styled>
				</div>
			</div>
			<div class="w-full">
				<x-input
						wire:model="form.activity"
						placeholder="Timeline entry..." />
			</div>
		</div>
		<div class="px-4 sm:px-6 lg:px-8">
			<div class="mt-8 flow-root">
				<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
					<div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
						<table class="relative min-w-full divide-y divide-gray-300 dark:divide-white/15">
							<thead>
							<tr>
								<th
										scope="col"
										class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-0 dark:text-white">
									Time
								</th>
								<th
										scope="col"
										class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
									Category
								</th>
								<th
										scope="col"
										class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
									Activity
								</th>
								<th
										scope="col"
										class="py-3.5 pr-4 pl-3 sm:pr-0">
									<span class="sr-only">Edit</span>
								</th>
							</tr>
							</thead>
							<tbody class="divide-y divide-gray-200 dark:divide-white/10">
							@forelse($activities as $activity)
								<tr wire:key="{{ $activity->id }}">
									<td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-0 dark:text-white">
										{{ $activity->entered_at->timezone(authUser()->timezone)->format('H:i:s') }}
									</td>
									<td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
										{{ $activity->type->label() }}
									</td>
									<td class="px-3 py-4 text-sm whitespace-normal text-gray-500 dark:text-gray-400 max-w-full">
										{{ $activity->activity }}
									</td>
									<td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-0">
										<x-button
												sm
												flat
												icon="pencil-square" />
										<x-button
												color="rose"
												sm
												flat
												icon="trash" />
									</td>
								</tr>
							@empty
								<p>Empty</p>
							@endforelse

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
