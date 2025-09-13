<?php

	use App\Enums\Subject\MoodLevels;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\ContactPoint\ContactPointFetchingService;
	use Livewire\Volt\Component;
	use Illuminate\View\View;

	new class extends Component {
		public Subject $primarySubject;
		public ?Negotiation $negotiation = null;
		public int $negotiationId;

		// Properties to store computed values
		public array $imageUrls = [];
		public $recentMoods = null;
		public $primaryPhoneNumber = null;
		public $primaryEmailAddress = null;

		public function mount($primarySubject, $negotiation = null)
		{
			$this->primarySubject = $primarySubject;
			$this->negotiation = $negotiation;
			$this->negotiationId = $negotiation? $negotiation->id : 0;

			$this->loadImageUrls();
			$this->loadRecentMoods();
			$this->loadPrimaryPhoneNumber();
			$this->loadPrimaryEmailAddress();
		}

		public function loadRecentMoods():void
		{
			if ($this->primarySubject) {
				// Load recent mood logs if not already loaded
				if (!$this->primarySubject->relationLoaded('moods')) {
					$this->recentMoods = $this->primarySubject->moods()->latest()->take(5)->get();
				} else {
					$this->recentMoods = $this->primarySubject->moods->sortByDesc('created_at')->take(5);
				}
			}
		}

		public function loadImageUrls()
		{
			$imageUrls = [];

			foreach ($this->primarySubject->images as $image) {
				$this->imageUrls[] = $image->url;
			}

			logger($this->imageUrls);
		}

		public function loadPrimaryPhoneNumber()
		{
			if ($this->primarySubject) {
				// Get all contact points for the subject
				$contactPoints = app(ContactPointFetchingService::class)->getContactPointsBySubject($this->primarySubject);

				// Filter to get only phone contact points
				$phoneContactPoints = $contactPoints->filter(function ($contactPoint) {
					return $contactPoint->kind === 'phone';
				});

				if ($phoneContactPoints->isNotEmpty()) {
					// Try to find a primary phone contact point
					$primaryPhone = $phoneContactPoints->firstWhere('is_primary', true);

					// If no primary phone is found, use the first phone
					if (!$primaryPhone) {
						$primaryPhone = $phoneContactPoints->first();
					}

					// Get the phone number from the related ContactPhone model
					if ($primaryPhone && $primaryPhone->phone) {
						$this->primaryPhoneNumber = $primaryPhone->phone->e164;
					}
				}
			}
		}

		public function loadPrimaryEmailAddress()
		{
			try {
				if (!$this->primarySubject) {
					return;
				}

				// Get all contact points for the subject
				$contactPoints = app(ContactPointFetchingService::class)->getContactPointsBySubject($this->primarySubject);

				if ($contactPoints->isEmpty()) {
					return;
				}

				// Filter to get only email contact points
				$emailContactPoints = $contactPoints->filter(function ($contactPoint) {
					return $contactPoint->kind === 'email';
				});

				if ($emailContactPoints->isEmpty()) {
					return;
				}

				// Try to find a primary email contact point, or use the first email as fallback
				$primaryEmail = $emailContactPoints->firstWhere('is_primary', true) ?? $emailContactPoints->first();

				// Get the email address from the related ContactEmail model
				if ($primaryEmail && isset($primaryEmail->email) && $primaryEmail->email && isset($primaryEmail->email->email)) {
					$this->primaryEmailAddress = $primaryEmail->email->email;
				} // Check for alternative ways the email might be stored
				elseif ($primaryEmail && isset($primaryEmail->address)) {
					$this->primaryEmailAddress = $primaryEmail->address;
				} elseif ($primaryEmail && isset($primaryEmail->value)) {
					$this->primaryEmailAddress = $primaryEmail->value;
				}
			} catch (\Exception $e) {
				// Silently handle exceptions to prevent page breaking
			}
		}

		public function editSubject()
		{
			return $this->redirect(route('subject.edit',
				[
					'subject' => $this->primarySubject,
					'negotiation' => $this->negotiation,
					'tenantSubdomain' => tenant()->subdomain
				]));
		}

		public function viewSubject()
		{
			return $this->redirect(route('subject.show',
				[
					'subject' => $this->primarySubject,
					'negotiation' => $this->negotiation,
					'tenantSubdomain' => tenant()->subdomain
				]));
		}

		public function getListeners()
		{
			return [
				"echo-private:negotiation.$this->negotiationId,.MoodCreated" => 'handleMoodCreated',
			];
		}


		public function handleMoodCreated($event)
		{
			// Refresh the component when a conversation is closed
			$this->loadRecentMoods();
		}
	}

?>

<div class="py-2 grid grid-cols-[1fr_1fr_1fr_1fr_1fr_3rem] gap-4 dark:bg-dark-800 p-4 mt-4">

	<div>
		<img
				class="rounded-lg w-32 h-32 object-cover"
				src="{{ $imageUrls[0] }}"
				alt="">
	</div>

	<div class="space-y-2">
		<p class="font-bold text-sm">Aliases</p>
		@if($primarySubject && is_array($primarySubject->alias))
			@foreach($primarySubject->alias as $alias)
				<p class="text-xs">
					{{ $alias }}
				</p>
			@endforeach
		@else
			<p class="text-xs">None</p>
		@endif
	</div>
	<div class="space-y-2">
		<p class="font-bold text-sm">Risk Factors</p>
		@if($primarySubject && is_array($primarySubject->risk_factors))
			@foreach($primarySubject->risk_factors as $riskFactor)
				<p class="text-xs">{{ $riskFactor }}</p>
			@endforeach
		@else
			<p class="text-xs">None</p>
		@endif
	</div>
	<div>
		<p class="font-bold text-sm">Moods</p>

		@if($recentMoods && $recentMoods->isNotEmpty())
			<div class="flex flex-wrap gap-1">
				@foreach($recentMoods as $moodLog)
					<span
							class="text-xl"
							title="{{ MoodLevels::from($moodLog->mood_level)->label() }} - {{ $moodLog->created_at->format('M d, H:i') }}">
						{{ MoodLevels::from($moodLog->mood_level)->icon() }}
					</span>
				@endforeach
			</div>
		@else
			<span class="text-xl">
				@if($primarySubject && $primarySubject->current_mood)
					{{ $primarySubject->current_mood->icon() }}
				@else
					😐
				@endif
			</span>
		@endif
	</div>
	<div class="flex justify-end items-start">
		<x-dropdown
				icon="ellipsis-vertical"
				static>
			<x-dropdown.items
					wire:click="editSubject"
					icon="pencil-square"
					text="Edit" />
			<x-dropdown.items
					wire:click="viewSubject"
					icon="eye"
					text="View" />
		</x-dropdown>
	</div>
</div>