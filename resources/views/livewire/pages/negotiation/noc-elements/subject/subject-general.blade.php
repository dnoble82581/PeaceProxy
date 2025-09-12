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

		public function loadImageUrls()
		{
			$this->imageUrls = [];

			if ($this->primarySubject) {
				if (!$this->primarySubject->relationLoaded('images')) {
					$this->primarySubject->load('images');
				}

				foreach ($this->primarySubject->images as $image) {
					// Prefer a non-empty property value; otherwise call the method
					$url = $image->url ?? null;

					if (!filled($url) && method_exists($image, 'url')) {
						$url = $image->url();
					}

					// Keep only non-empty, non-whitespace strings
					if (is_string($url) && trim($url) !== '') {
						$this->imageUrls[] = $url;
					}
				}

				// De-dup and reindex (optional hardening)
				$this->imageUrls = array_values(array_unique($this->imageUrls));

				// Fallback if we still have nothing
				if (empty($this->imageUrls)) {
					$this->imageUrls[] = $this->primarySubject->temporaryImageUrl();
				}
			}
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
	<div class="flex col-span-2 gap-4">
		<div
				class="inline text-center"
				x-data="{
           imageUrls: @js($imageUrls),
           currentIndex: 0,
           totalImages: @js(count($imageUrls)),
            showPhoneModal: false,

            nextImage() {
                this.currentIndex = (this.currentIndex + 1) % this.totalImages;
            },

            prevImage() {
                this.currentIndex = (this.currentIndex - 1 + this.totalImages) % this.totalImages;
            },

            currentImageUrl() {
                return this.imageUrls[this.currentIndex];
            },

            openPhoneModal() {
                this.showPhoneModal = true;
            },

            closePhoneModal() {
                this.showPhoneModal = false;
            }
        }">
			<!-- Image with hover buttons -->
			<div class="relative group">
				<img
						class="rounded-lg size-24"
						:src="currentImageUrl()"
						alt="">
				<!-- Overlay with buttons that appear on hover -->
				<div class="absolute inset-0 bg-black/50 bg-opacity-25 rounded-lg flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
					<button
							@click="openPhoneModal()"
							class="bg-white dark:bg-dark-700 rounded-full p-2 hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors">
						<x-icon
								name="phone"
								class="size-5 text-primary-500" />
					</button>

					<a
							href="{{ $primaryEmailAddress ? 'mailto:' . $primaryEmailAddress : '#' }}"
							onclick="{{ $primaryEmailAddress ? 'console.log(\'Email button clicked. Email address: ' . $primaryEmailAddress . '\')' : 'console.log(\'No email address available\')' }}"
							title="{{ $primaryEmailAddress ? 'Send email to: ' . $primaryEmailAddress : 'No email address available' }}"
							class="bg-white dark:bg-dark-700 rounded-full p-2 hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors {{ !$primaryEmailAddress ? 'opacity-50 cursor-not-allowed' : '' }}">
						<x-icon
								name="envelope"
								class="size-5 text-primary-500" />
					</a>
				</div>
			</div>

			<!-- Phone functionality coming soon modal -->
			<div
					x-show="showPhoneModal"
					x-cloak
					class="fixed inset-0 z-50 overflow-y-auto"
					aria-labelledby="modal-title"
					role="dialog"
					aria-modal="true">
				<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
					<!-- Background overlay -->
					<div
							x-show="showPhoneModal"
							x-transition:enter="ease-out duration-300"
							x-transition:enter-start="opacity-0"
							x-transition:enter-end="opacity-100"
							x-transition:leave="ease-in duration-200"
							x-transition:leave-start="opacity-100"
							x-transition:leave-end="opacity-0"
							class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
							@click="closePhoneModal()"
							aria-hidden="true">
					</div>

					<!-- Modal panel -->
					<div
							x-show="showPhoneModal"
							x-transition:enter="ease-out duration-300"
							x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
							x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
							x-transition:leave="ease-in duration-200"
							x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
							x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
							class="inline-block align-bottom bg-white dark:bg-dark-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
						<div class="bg-white dark:bg-dark-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
							<div class="sm:flex sm:items-start">
								<div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
									<x-icon
											name="phone"
											class="h-6 w-6 text-blue-600 dark:text-blue-400" />
								</div>
								<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
									<h3
											class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
											id="modal-title">
										Phone Functionality
									</h3>
									<div class="mt-2">
										<p class="text-sm text-gray-500 dark:text-gray-400">
											This functionality is coming soon! We're working hard to bring you direct
											phone integration in a future update.
										</p>
									</div>
								</div>
							</div>
						</div>
						<div class="bg-gray-50 dark:bg-dark-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
							<button
									type="button"
									class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm"
									@click="closePhoneModal()">
								Got it
							</button>
						</div>
					</div>
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