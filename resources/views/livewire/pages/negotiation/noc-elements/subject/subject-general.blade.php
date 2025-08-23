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

		public function mount($primarySubject, $negotiation = null)
		{
			$this->primarySubject = $primarySubject;
			$this->negotiation = $negotiation;
			$this->negotiationId = $negotiation? $negotiation->id : 0;

			$this->loadImageUrls();
			$this->loadRecentMoods();
			$this->loadPrimaryPhoneNumber();
		}

		public function loadImageUrls()
		{
			$this->imageUrls = [];

			// Only proceed if primarySubject is not null
			if ($this->primarySubject) {
				// Load images if not already loaded
				if (!$this->primarySubject->relationLoaded('images')) {
					$this->primarySubject->load('images');
				}

				// Get all image URLs
				foreach ($this->primarySubject->images as $image) {
					// Check if url property exists and is not null
					if (isset($image->url)) {
						$this->imageUrls[] = $image->url;
					} else {
						// Fall back to url() method
						$this->imageUrls[] = $image->url();
					}
				}

				// If no images, use the temporary image URL
				if (empty($this->imageUrls)) {
					$this->imageUrls[] = $this->primarySubject->temporaryImageUrl();
				}
			}
		}

		public function loadRecentMoods()
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
	<div class="flex col-span-2 gap-4">
		<div
				class="inline text-center"
				x-data="{
            imageUrls: {{ json_encode($imageUrls) }},
            currentIndex: 0,
            totalImages: {{ count($imageUrls) }},

            nextImage() {
                this.currentIndex = (this.currentIndex + 1) % this.totalImages;
            },

            prevImage() {
                this.currentIndex = (this.currentIndex - 1 + this.totalImages) % this.totalImages;
            },

            currentImageUrl() {
                return this.imageUrls[this.currentIndex];
            }
        }">
			<!-- Image with hover buttons -->
			<div class="relative group">
				<img
						class="rounded-lg size-24"
						x-bind:src="currentImageUrl()"
						alt="">
				<!-- Overlay with buttons that appear on hover -->
				<div class="absolute inset-0 bg-black/50 bg-opacity-25 rounded-lg flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
					<button
							wire:click="$dispatch('start-call-timer')"
							class="bg-white dark:bg-dark-700 rounded-full p-2 hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors">
						<x-icon
								name="phone"
								class="size-5 text-primary-500" />
					</button>

					<a
							href="{{ route('dev.call.ui', ['tenantSubdomain' => tenant()->subdomain]) }}"
							class="bg-white dark:bg-dark-700 rounded-full p-2 hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors">
						<x-icon
								name="envelope"
								class="size-5 text-primary-500" />
					</a>

				</div>
			</div>

			<!-- Buttons container -->
			<div class="mt-2 flex justify-between items-center w-fit">
				<button
						class="hover:bg-gray-200 dark:hover:bg-gray-700 hover:cursor-pointer p-1 rounded-lg"
						x-on:click="prevImage()"
						x-bind:disabled="totalImages <= 1">
					<x-icon
							class="size-4"
							name="chevron-left" />
				</button>
				<p
						class="px-2 text-xs"
						x-text="`${currentIndex + 1} of ${totalImages}`"></p>
				<button
						class="hover:bg-gray-200 dark:hover:bg-gray-700 hover:cursor-pointer p-1 rounded-lg"
						x-on:click="nextImage()"
						x-bind:disabled="totalImages <= 1">
					<x-icon
							class="size-4 hover:cursor-pointer"
							name="chevron-right" />
				</button>
			</div>
		</div>
		<div class="space-y-2">
			<p class="text-sm">
				<span class="font-bold text-primary-500">{{ $primarySubject ? ($primarySubject->name ?? 'Unknown') : 'Unknown' }}</span>
			</p>
			<p class="text-xs">
				<span class="font-bold">Age:</span> {{ $primarySubject ? ($primarySubject->subjectAge() ?? 'Unknown') : 'Unknown' }}
			</p>
			<p class="text-xs">
				<span class="font-bold">Gender:</span> {{ $primarySubject ? ($primarySubject->gender ?? 'Unknown') : 'Unknown' }}
			</p>
			<div class="text-xs flex gap-1">
				<span class="font-bold">Phone:</span>
				@if($primaryPhoneNumber)
					<button class="hover:cursor-pointer text-primary-400 hover:text-primary-500 mt-0.5">{{ $primaryPhoneNumber }}</button>
				@else
					<span>None</span>
				@endif
			</div>
		</div>
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