<?php

	use App\Models\Subject;
	use App\Models\ContactPoint;
	use App\Services\Subject\SubjectFetchingService;
	use App\Services\ContactPoint\ContactPointDeletionService;
	use App\Support\EventNames\SubjectEventNames;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;

	new class extends Component {
		public ?Subject $subject = null;
		public int $negotiationId;

		public bool $showCreateModal = false;
		public bool $showContactEditModal = false;

		public function mount($subjectId, $negotiationId)
		{
			$this->subject = $this->fetchSubject($subjectId);
			$this->negotiationId = (int) $negotiationId;
		}

		private function fetchSubject($subjectId)
		{
			if (empty($subjectId)) {
				return null;
			}
			// First, get the subject with just the basic contact points data
			$subject = Subject::query()
				->with(['contactPoints:id,contactable_id,contactable_type,tenant_id,kind,label,is_primary,is_verified,priority'])
				->select('id', 'name')
				->find($subjectId);

			if (!$subject) {
				return null;
			}

			// Then, load the specific relationship data for each contact point based on its kind
			$emailContactPoints = $subject->contactPoints->where('kind', 'email')->pluck('id');
			$phoneContactPoints = $subject->contactPoints->where('kind', 'phone')->pluck('id');
			$addressContactPoints = $subject->contactPoints->where('kind', 'address')->pluck('id');

			// Load relationships only for the relevant contact points
			if ($emailContactPoints->isNotEmpty()) {
				ContactPoint::whereIn('id', $emailContactPoints)->with('email')->get()
					->each(function ($cp) use ($subject) {
						$subject->contactPoints->where('id', $cp->id)->first()->setRelation('email', $cp->email);
					});
			}

			if ($phoneContactPoints->isNotEmpty()) {
				ContactPoint::whereIn('id', $phoneContactPoints)->with('phone')->get()
					->each(function ($cp) use ($subject) {
						$subject->contactPoints->where('id', $cp->id)->first()->setRelation('phone', $cp->phone);
					});
			}

			if ($addressContactPoints->isNotEmpty()) {
				ContactPoint::whereIn('id', $addressContactPoints)->with('address')->get()
					->each(function ($cp) use ($subject) {
						$subject->contactPoints->where('id', $cp->id)->first()->setRelation('address', $cp->address);
					});
			}

			return $subject;
		}

		public function deleteContactPoint($contactPointId):void
		{
			if (!$this->subject) {
				return;
			}
			app(ContactPointDeletionService::class)->deleteContactPoint($contactPointId);
			$this->refreshSubject();
		}

		public function editContactPoint(int $id)
		{
			if (!$this->subject) {
				return;
			}
			$this->dispatch('load-contact', contactPointId: $id)->to('forms.contact.edit-contact-point');
			$this->showContactEditModal = true;
		}

		/**
		 * Refresh the subject data after a change
		 */
		private function refreshSubject()
		{
			if (!$this->subject) {
				return;
			}
			$this->subject = $this->fetchSubject($this->subject->id);
		}

		public function handleContactCreated(array $event)
		{
			if (!$this->subject) {
				return;
			}
			$this->refreshSubject();
		}

		public function handleContactDeleted(array $event)
		{
			if (!$this->subject) {
				return;
			}
			$this->refreshSubject();
		}

		public function handleContactUpdated(array $event)
		{
			if (!$this->subject) {
				return;
			}
			$this->refreshSubject();
		}

		public function getListeners()
		{
			if (!$this->subject) {
				return [];
			}
			return [
				'echo-private:'.\App\Support\Channels\Subject::subjectContact($this->subject->id).',.'.SubjectEventNames::CONTACT_CREATED => 'handleContactCreated',
				'echo-private:'.\App\Support\Channels\Subject::subjectContact($this->subject->id).',.'.SubjectEventNames::CONTACT_DELETED => 'handleContactDeleted',
				'echo-private:'.\App\Support\Channels\Subject::subjectContact($this->subject->id).',.'.SubjectEventNames::CONTACT_UPDATED => 'handleContactUpdated',
			];
		}

		#[On('closeModal')]
		public function closeModal()
		{
			$this->showCreateModal = false;
			$this->showContactEditModal = false;
		}

		public function createContact()
		{
			$this->showCreateModal = true;
		}
	}

?>

<div>
	@if($subject)
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
						Label
					</th>
					<th
							scope="col"
							class="hidden px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100 md:table-cell">
						Contact Information
					</th>
					<th
							scope="col"
							class="px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100">
						Primary
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
									wire:navigate.hover
									color=""
									wire:click="createContact"
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
				@forelse($this->subject->contactPoints as $contactPoint)
					<tr>
						<td class="relative pl-3 text-xs font-medium text-primary-950 dark:text-dark-100">
							{{ ucfirst($contactPoint->kind) }}
							<div class="absolute right-full bottom-0 h-px w-screen bg-gray-100"></div>
							<div class="absolute bottom-0 left-0 h-px w-screen bg-gray-100"></div>
						</td>
						<td class="hidden px-3 py-2 text-xs dark:text-dark-400 text-gray-500 sm:table-cell">
							{{ $contactPoint->label ?: 'N/A' }}
						</td>
						<td class="hidden px-3 py-2 text-xs dark:text-dark-400 text-gray-500 md:table-cell">
							@if($contactPoint->kind === 'email')
								{{ $contactPoint->email->email ?? '' }}
							@elseif($contactPoint->kind === 'phone')
								{{ $contactPoint->phone->e164 }} {{ $contactPoint->phone->ext ? 'ext. ' . $contactPoint->phone->ext : '' }}
							@elseif($contactPoint->kind === 'address')
								{{ $contactPoint->address->address1 }}
								{{ $contactPoint->address->address2 ? ', ' . $contactPoint->address->address2 : '' }}
								{{ $contactPoint->address->city ? ', ' . $contactPoint->address->city : '' }}
								{{ $contactPoint->address->region ? ', ' . $contactPoint->address->region : '' }}
								{{ $contactPoint->address->postal_code ? ' ' . $contactPoint->address->postal_code : '' }}
							@endif
						</td>
						<td class="px-3 py-2 text-xs dark:text-dark-400 text-gray-500">
							{{ $contactPoint->is_primary ? 'Yes' : 'No' }}
						</td>
						<td class="text-right">
							@if($contactPoint->kind === 'phone')
								<x-button.circle
										wire:click="$dispatch('start-call-timer')"
										flat
										color="green"
										icon="phone"
										sm
										title="Start call timer" />
							@endif
							<x-button.circle
									wire:navigate.hover
									wire:click="editContactPoint({{ $contactPoint->id }})"
									flat
									color="sky"
									icon="pencil-square"
									sm />
							<x-button.circle
									wire:click="deleteContactPoint({{ $contactPoint->id }})"
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
							No contacts found for this subject.
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
								top-right corner to create a new contact.
							</p>
						</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>
	</div>
	@else
		<div class="p-4 text-sm text-gray-600 dark:text-dark-300">No contacts available.</div>
	@endif
	@isset($subject)
	<template x-teleport="body">
		<x-slide
				wire="showCreateModal"
				class="">
			<x-slot:title>
				<div class="px-4 sm:px-8 text-center space-y-3">
					<h1 class="text-2xl text-gray-400 font-semibold uppercase">Create Contact Point</h1>
					<p class="text-xs">Creating a contact point for:
						<span class="text-primary-400">{{ $subject->name }}</span></p>
				</div>
			</x-slot:title>
			<livewire:forms.contact.create-contact-point
					:subject-id="$subject->id"
					:negotiation-id="$negotiationId" />
		</x-slide>
	</template>
	<template x-teleport="body">
		<x-slide
				wire="showContactEditModal"
				class="">
			<x-slot:title>
				<div class="px-4 sm:px-8 text-center space-y-3">
					<h1 class="text-2xl text-gray-400 font-semibold uppercase">Edit Contact Point</h1>
					<p class="text-xs">Editing a contact point for:
						<span class="text-primary-400">{{ $subject->name }}</span></p>
				</div>
			</x-slot:title>
			<livewire:forms.contact.edit-contact-point
					:negotiation-id="$negotiationId"
					:subject-id="$subject->id" />
		</x-slide>
	</template>
	@endisset
</div>