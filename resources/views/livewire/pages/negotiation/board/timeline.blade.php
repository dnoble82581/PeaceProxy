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

		public int $editingId = 0;

		public function mount(int $negotiationId, int $subjectId)
		{
			$this->negotiationId = $negotiationId;
			$this->subjectId = $subjectId;
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

			if ($this->editingId) {
				$existingDto = app(App\Services\Activity\ActivityFetchingService::class)->getActivityDTO($this->editingId);
				$dto = new App\DTOs\Activity\ActivityDTO(
					tenant_id: $validated['tenant_id'],
					negotiation_id: $validated['negotiation_id'],
					user_id: $validated['user_id'],
					subject_id: $validated['subject_id'],
					type: isset($validated['type'])? ActivityType::tryFrom($validated['type']) : ($existingDto?->type ?? null),
					activity: $validated['activity'],
					is_flagged: (bool) ($existingDto?->is_flagged ?? false),
					entered_at: $existingDto?->entered_at ?? now(),
					created_at: $existingDto?->created_at,
					updated_at: now()
				);

				app(App\Services\Activity\ActivityUpdateService::class)->updateActivity($dto, $this->editingId);
				$this->editingId = 0;
				$this->form->reset();
				$this->loadActivities();
				return;
			}

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

		public function editActivity(int $activityId)
		{
			$activity = app(App\Services\Activity\ActivityFetchingService::class)->getActivity($activityId);
			if (!$activity) {
				return;
			}
			$this->editingId = $activity->id;
			$this->form->type = $activity->type?->value;
			$this->form->activity = $activity->activity;
		}

		public function cancelEdit()
		{
			$this->editingId = 0;
			$this->form->reset();
		}

		public function deleteActivity(int $activityId)
		{
			app(App\Services\Activity\ActivityDeletionService::class)->deleteActivity($activityId);
			$this->loadActivities();
		}
	}

?>

<div>
	<form wire:submit.prevent="saveTimeline">
		<div class="flex gap-4 items-center px-4">
			<div class="flex gap-4 items-center">
				<x-button
						text="{{ $editingId ? 'Update' : 'Save' }}"
						type="submit" />
				@if($editingId)
					<x-button
							flat
							color="slate"
							text="Cancel"
							wire:click="cancelEdit" />
				@endif
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
								<tr
										wire:key="{{ $activity->id }}"
										class="even:bg-gray-50 dark:even:bg-dark-700/50">
									<td class="py-2 px-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-0 dark:text-white">
										{{ $activity->entered_at->timezone(authUser()->timezone)->format('H:i:s') }}
									</td>
									<td class="px-3 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
										{{ $activity->type->label() }}
									</td>
									<td class="px-3 py-2 text-sm whitespace-normal text-gray-500 dark:text-gray-400 max-w-full">
										{{ $activity->activity }}
									</td>
									<td class="py-2 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-0">
										<x-button
												sm
												flat
												icon="pencil-square"
												wire:click="editActivity({{ $activity->id }})" />
										<x-button
												color="rose"
												sm
												flat
												icon="trash"
												wire:click="deleteActivity({{ $activity->id }})" />
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
