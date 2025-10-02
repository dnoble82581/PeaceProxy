<?php

	use App\Enums\Warrant\BondType;
	use App\Enums\Warrant\WarrantStatus;
	use App\Enums\Warrant\WarrantType;
	use App\Factories\MessageFactory;
	use App\Livewire\Forms\CreateWarrantForm;
	use App\Models\Subject;
	use App\Models\Warrant;
	use App\Services\Warrant\WarrantDestructionService;
	use App\Services\Warrant\WarrantFetchingService;
	use App\Support\EventNames\SubjectEventNames;
	use Illuminate\Support\Collection;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use TallStackUi\Traits\Interactions;

	new class extends Component {
		public Subject $subject;
		public CreateWarrantForm $form;
		public int $negotiationId;
		public bool $showCreateWarrantModal = false;
		public bool $showUpdateWarrantModal = false;

		use Interactions;


		public function mount($subjectId, $negotiationId)
		{
			$this->subject = $this->fetchSubject($subjectId);
			$this->form->tenant_id = auth()->user()->tenant_id;
			$this->form->subject_id = $this->subject->id;
			$this->negotiationId = $negotiationId;
		}

		private function fetchSubject($subjectId)
		{
			return Subject::query()
				->with([
					'warrants' => function ($query) {
						$query->select('id', 'subject_id', 'type', 'status', 'offense_description', 'bond_amount',
							'bond_type');
					}
				])
				->select('id', 'name')
				->findOrFail($subjectId);
		}

		public function deleteWarrant($warrantId):void
		{
			app(WarrantDestructionService::class)->deleteWarrant($warrantId);

			$this->subject->load('warrants');

		}

		public function createWarrant()
		{
			$this->showCreateWarrantModal = true;
		}

		#[\Livewire\Attributes\On('close-modal')]
		public function closeModals()
		{
			$this->showCreateWarrantModal = false;
			$this->showUpdateWarrantModal = false;
		}

		public function updateWarrant($warrantId):void
		{
			$this->warrantToEditId = $warrantId;

			// Tell the child to load the warrant (no remount)
			$this->dispatch('load-warrant', id: $warrantId)->to('pages.subject.update-warrant');

			$this->showUpdateWarrantModal = true;
		}

		public function handleWarrantCreated(array $event):void
		{
			$this->subject->load('warrants');

			$warrant = app(WarrantFetchingService::class)->getWarrantById($event['warrantId']);

			if (!$warrant) {
				return;
			}

			$messageFactory = app(MessageFactory::class);
			$message = $messageFactory->generateMessage($warrant, 'WarrantCreated');

			$this->toast()->timeout()->info($message)->send();

		}

		public function handleWarrantDeleted(array $event):void
		{
			$details = $event['details'] ?? null;
			if ($details) {
				$message = "{$details['createdBy']} deleted a {$details['label']} warrant for {$details['subjectName']}.";
			} else {
				$message = "A warrant has been deleted.";
			}

			$this->toast()->timeout()->info($message)->send();
			$this->subject->load('warrants');
		}

		public function handleWarrantUpdated(array $event)
		{
			$this->subject->load('warrants');

			$warrant = app(WarrantFetchingService::class)->getWarrantById($event['warrantId']);

			if (!$warrant) {
				return;
			}

			$messageFactory = app(MessageFactory::class);
			$message = $messageFactory->generateMessage($warrant, 'WarrantUpdated');

			$this->toast()->timeout()->info($message)->send();
		}

		public function getListeners()
		{
			$subjectId = $this->subject->id;
			return [
				'echo-private:'.\App\Support\Channels\Subject::subjectWarrant($subjectId).',.'.SubjectEventNames::WARRANT_CREATED => 'handleWarrantCreated',
				'echo-private:'.\App\Support\Channels\Subject::subjectWarrant($subjectId).',.'.SubjectEventNames::WARRANT_DELETED => 'handleWarrantDeleted',
				'echo-private:'.\App\Support\Channels\Subject::subjectWarrant($subjectId).',.'.SubjectEventNames::WARRANT_UPDATED => 'handleWarrantUpdated',
			];
		}
	}

?>

<div>
	<div class="mt-2 flow-root overflow-hidden rounded-t-lg">
		<div class="">
			<table class="w-full text-left">
				<thead class="dark:bg-dark-600">
				<tr>
					<th
							scope="col"
							class="relative isolate px-3 text-left text-xs font-semibold text-primary-950 dark:text-dark-100">
						Type
						<div class="absolute inset-y-0 right-full -z-10 w-screen border-b border-b-gray-200"></div>
						<div class="absolute inset-y-0 left-0 -z-10 w-screen border-b border-b-gray-200"></div>
					</th>
					<th
							scope="col"
							class="hidden px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100 sm:table-cell">
						Status
					</th>
					<th
							scope="col"
							class="hidden px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100 md:table-cell">
						Offense Description
					</th>
					<th
							scope="col"
							class="px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100">Bond
					                                                                                              Amount
					</th>
					<th
							scope="col"
							class="py-2 pl-3">
						<span class="sr-only">Edit</span>
					</th>
					<th
							scope="col"
							class="relative">
						<div>
							<x-button.circle
									color=""
									wire:click="createWarrant"
									sm
									flat
									icon="plus"
									type="button">
							</x-button.circle>
						</div>
					</th>

				</tr>
				</thead>
				<tbody>
				@forelse($this->subject->warrants as $warrant)
					<tr>
						<td class="relative pl-3 text-xs font-medium text-primary-950 dark:text-dark-100">
							{{ $warrant->type->label() }}
							<div class="absolute right-full bottom-0 h-px w-screen bg-gray-100"></div>
							<div class="absolute bottom-0 left-0 h-px w-screen bg-gray-100"></div>
						</td>
						<td class="hidden px-3 py-2 text-xs dark:text-dark-400 text-gray-500 sm:table-cell">{{ $warrant->status->label() }}
						</td>
						<td class="hidden px-3 py-2 text-xs dark:text-dark-400 text-gray-500 md:table-cell">
							{{ $warrant->offense_description }}
						</td>
						<td class="px-3 py-2 text-xs dark:text-dark-400 text-gray-500">
							{{ $warrant->bondAmount() }}
						</td>
						<td class="text-right">
							<x-button.circle
									wire:click="updateWarrant({{ $warrant->id }})"
									flat
									color="sky"
									icon="pencil-square"
									sm />
							<x-button.circle
									wire:click="deleteWarrant({{ $warrant->id }})"
									flat
									color="red"
									icon="trash"
									sm />
						</td>
					</tr>
				@empty
					<tr>
						<td
								colspan="5"
								class="text-center p-4 text-gray-500">
							No warrants found for this subject.
							<p class="mt-2">
								Click the <span class="inline-flex items-center"><svg
											class="w-4 h-4 text-gray-500"
											fill="none"
											stroke="currentColor"
											viewBox="0 0 24 24"
											xmlns="http://www.w3.org/2000/svg"><path
												stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg></span> button in the
								top-right corner to create a new warrant.
							</p>
						</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>
	</div>
	<template x-teleport="body">
		<x-slide
				size="3xl"
				wire="showCreateWarrantModal"
				class="">
			<x-slot:title>
				<div class="text-center">
					<h1 class="text-2xl text-gray-600 dark:text-gray-400 font-semibold uppercase">Create Warrant</h1>
					<p class="text-xs">Creating a warrant for:
						<span class="text-primary-400">{{ $subject->name }}</span></p>
				</div>
			</x-slot:title>
			<livewire:pages.subject.create-warrant
					:subject="$subject"
					:negotiationId="$negotiationId" />
		</x-slide>
	</template>

	{{-- Remove the @if($this->warrantToEditId) guard so the slide exists from the start --}}
	<template x-teleport="body">
		<x-slide
				id="update-warrant-slide"
				size="3xl"
				wire="showUpdateWarrantModal"
				x-on:open="$focusOn('edit-warrant-first-field', 400)" {{-- optional: see (C) --}}
		>
			<x-slot:title>
				<div class="text-center">
					<h1 class="text-2xl text-gray-600 dark:text-gray-400 font-semibold uppercase">Edit Warrant</h1>
					<p class="text-xs">
						Editing a warrant for:
						<span class="text-primary-400">{{ $subject->name }}</span>
					</p>
				</div>
			</x-slot:title>

			{{-- Keep this child mounted; do NOT pass :warrant-id so it won’t remount on first open --}}
			<div wire:ignore>
				<livewire:pages.subject.update-warrant
						:negotiation-id="$negotiationId"
						wire:key="update-warrant-slide"
				/>
			</div>
		</x-slide>
	</template>
</div>
