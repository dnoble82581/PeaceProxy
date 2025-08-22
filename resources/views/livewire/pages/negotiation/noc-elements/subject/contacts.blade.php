<?php

	use App\Models\Subject;
	use App\Models\ContactPoint;
	use App\Services\Subject\SubjectFetchingService;
	use App\Services\ContactPoint\ContactPointDeletionService;
	use Livewire\Volt\Component;

	new class extends Component {
		public Subject $subject;
		public int $negotiationId;

		public function mount($subjectId, $negotiationId)
		{
			$this->subject = $this->fetchSubject($subjectId);
			$this->negotiationId = $negotiationId;
		}

		private function fetchSubject($subjectId)
		{
			// First, get the subject with just the basic contact points data
			$subject = Subject::query()
				->with(['contactPoints:id,contactable_id,contactable_type,tenant_id,kind,label,is_primary,is_verified,priority'])
				->select('id', 'name')
				->findOrFail($subjectId);

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
			app(ContactPointDeletionService::class)->deleteContactPoint($contactPointId);
			$this->refreshSubject();
		}

		/**
		 * Refresh the subject data after a change
		 */
		private function refreshSubject()
		{
			$this->subject = $this->fetchSubject($this->subject->id);
		}
	}

?>

<div x-data="{}">
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
									href="{{ route('contact-point.create', ['tenantSubdomain' => tenant()->subdomain, 'negotiationId' => $negotiationId, 'subjectId' => $subject->id]) }}"
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
				@foreach($this->subject->contactPoints as $contactPoint)
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
								{{ $contactPoint->email->email }}
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
									href="{{ route('contact-point.edit', ['tenantSubdomain' => tenant()->subdomain, 'negotiationId' => $negotiationId, 'subjectId' => $subject->id, 'contactPointId' => $contactPoint->id]) }}"
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
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>