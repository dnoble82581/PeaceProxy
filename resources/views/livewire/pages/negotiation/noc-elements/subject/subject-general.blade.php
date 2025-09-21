<?php

	use App\Enums\Subject\MoodLevels;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\ContactPoint\ContactPointFetchingService;
	use Livewire\Volt\Component;
	use Illuminate\View\View;
	use TallStackUi\Traits\Interactions;


	new class extends Component {
		use Interactions;

		public Subject $primarySubject;
		public ?Negotiation $negotiation = null;
		public int $negotiationId;

		// Properties to store computed values
		public array $imageUrls = [];
		public $recentMoods = null;
		public $primaryPhoneNumber = null;
		public $primaryEmailAddress = null;
		public bool $hasPhoneNumber = false;
		public bool $hasEmailAddress = false;

		// Modal control
		public bool $showPhoneIntegrationModal = false;

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
					$this->recentMoods = $this->primarySubject->moods()->latest()->take(8)->get();
				} else {
					$this->recentMoods = $this->primarySubject->moods->sortByDesc('created_at')->take(5);
				}
			}
		}

		public function loadImageUrls()
		{
			// Clear the array first to avoid duplicates if the method is called multiple times
			$this->imageUrls = [];

			// If the subject has a primary image, add it first
			$primaryImageUrl = $this->primarySubject->primaryImage();
			if ($primaryImageUrl && $primaryImageUrl !== $this->primarySubject->temporaryImageUrl()) {
				$this->imageUrls[] = $primaryImageUrl;
			}

			// Add all other images, skipping the primary one if it's already added
			foreach ($this->primarySubject->images as $image) {
				// Break if we've already added 5 images
				if (count($this->imageUrls) >= 5) {
					break;
				}

				$imageUrl = $image->url ?? (method_exists($image, 'url')? $image->url() : null);
				if ($imageUrl && $imageUrl !== $primaryImageUrl) {
					$this->imageUrls[] = $imageUrl;
				}
			}

			// If no images were added, use the temporary image URL (but this shouldn't exceed the limit of 5)
			if (empty($this->imageUrls) && count($this->imageUrls) < 5) {
				$this->imageUrls[] = $this->primarySubject->temporaryImageUrl();
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

				// Set hasPhoneNumber based on whether any phone contact points exist
				$this->hasPhoneNumber = $phoneContactPoints->isNotEmpty();

				if ($this->hasPhoneNumber) {
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

				// Set hasEmailAddress based on whether any email contact points exist
				$this->hasEmailAddress = !$emailContactPoints->isEmpty();

				if (!$this->hasEmailAddress) {
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

		public function getListeners():array
		{
			$subjectId = $this->primarySubject?->id;

			if (!$subjectId) {
				return [];
			}
			return
				[
					'echo-private:'.\App\Support\Channels\Subject::subjectMood($subjectId).',.'.\App\Support\EventNames\SubjectEventNames::MOOD_CREATED => 'handleMoodCreated',
					'echo-private:'.\App\Support\Channels\Subject::subject($subjectId).',.'.\App\Support\EventNames\SubjectEventNames::SUBJECT_UPDATED => 'refresh',
				];
		}

		public function handleSubjectUpdated(array $event) {}

		public function handleMoodCreated($event)
		{

			$this->loadRecentMoods();
			$newMoodEnum = MoodLevels::from((int) $event['mood']['mood_level']);
			$newMoodLabel = $newMoodEnum->label();
			$newMoodIcon = $newMoodEnum->icon();

			if ($this->isMoodLoggedByAuthUser($event['mood']['logged_by_id'])) {
				$this->sendToastToThisUser($newMoodLabel, $newMoodIcon);
			} else {
				$this->sendToastForOtherUser($event['mood']['logged_by_id'], $newMoodLabel, $newMoodIcon);
			}
		}

		protected function sendToast(string $message):void
		{
			$this->toast()->success($message)->send();
		}

		protected function sendToastToThisUser($newMoodLabel, $newMoodIcon)
		{

			$criticalLabels = ['Severely Depressed', 'Suicidal', 'Hypomanic', 'Manic'];
			$moderateLabels = ['Depressed', 'Sad', 'Happy', 'Euphoric'];
			$message = "You updated this subject's mood to {$newMoodLabel} {$newMoodIcon}";

			if (in_array((string) $newMoodLabel, $criticalLabels, true)) {
				$this->toast()->error($message)->send();
			} elseif (in_array((string) $newMoodLabel, $moderateLabels, true)) {
				$this->toast()->warning($message)->send();
			} else {
				$this->toast()->info($message)->send();
			}

		}

		protected function sendToastForOtherUser($loggedById, $newMoodLabel, $newMoodIcon):void
		{
			// Ensure we have the minimal data to build a meaningful message
			if (empty($loggedById) || empty($newMoodLabel) || empty($newMoodIcon)) {
				$this->toast()->info("The subject's mood was updated.")->send();
				return;
			}

			$loggedBy = $this->getUserById((int) $loggedById);
			$name = $loggedBy?->name ?? 'Someone';
			$message = "{$name} updated this subject's mood to {$newMoodLabel} {$newMoodIcon}";

			// Labels that should trigger a warning toast
			$criticalLabels = ['Severely Depressed', 'Suicidal', 'Hypomanic', 'Manic'];
			$moderateLabels = ['Depressed', 'Sad', 'Happy', 'Euphoric'];

			if (in_array((string) $newMoodLabel, $criticalLabels, true)) {
				$this->toast()->error($message)->send();
			} elseif (in_array((string) $newMoodLabel, $moderateLabels, true)) {
				$this->toast()->warning($message)->send();
			} else {
				$this->toast()->info($message)->send();
			}
		}

		/**
		 * Get the user instance by ID (with fallback handling).
		 */
		protected function getUserById(int $userId):?\App\Models\User
		{
			return \App\Models\User::find($userId);
		}


		/**
		 * Determine if the mood was logged by the authenticated user.
		 */
		protected function isMoodLoggedByAuthUser(int $loggedById):bool
		{
			return isAuthUser($loggedById);
		}


		public function refreshSubjectAssets():void
		{
			// When any negotiation activity occurs, refresh subject-related assets that may change
			$this->loadImageUrls();
		}

		public function openPhoneIntegrationModal()
		{
			$this->dispatch('togglePhoneModal');
		}

		public function closePhoneIntegrationModal()
		{
			$this->showPhoneIntegrationModal = false;
		}
	}
?>

<div class="py-4 px-2 grid grid-cols-1 md:grid-cols-[150px_1fr_1fr_1fr_1fr_20px] gap-4 dark:bg-dark-800 bg-white rounded-lg shadow-sm">
	<div
			x-data="{
			activeSlide: 0,
			isHovering: false,
			slides: {{ json_encode(count($imageUrls) > 0 ? $imageUrls : [$primarySubject->temporaryImageUrl()]) }},
			nextSlide() {
				this.activeSlide = (this.activeSlide + 1) % this.slides.length;
			},
			prevSlide() {
				this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
			}
		}"
			class="relative w-32 h-32"
			@mouseenter="isHovering = true"
			@mouseleave="isHovering = false"
	>
		<!-- Slider container -->
		<div class="overflow-hidden rounded-lg w-32 h-32 relative shadow-md">
			<!-- Slides -->
			<template
					x-for="(slide, index) in slides"
					:key="index">
				<div
						x-show="activeSlide === index"
						x-transition:enter="transition ease-out duration-300"
						x-transition:enter-start="opacity-0 transform scale-90"
						x-transition:enter-end="opacity-100 transform scale-100"
						x-transition:leave="transition ease-in duration-300"
						x-transition:leave-start="opacity-100 transform scale-100"
						x-transition:leave-end="opacity-0 transform scale-90"
						class="absolute inset-0"
				>
					<div
							class="absolute flex justify-between w-full bottom-1 left-0 px-2"
							x-show="isHovering">
						<x-button.circle
								:disabled="!$hasPhoneNumber"
								wire:click="openPhoneIntegrationModal"
								sm
								icon="phone"
								color="primary" />
						<x-button.circle
								:disabled="!$hasEmailAddress"
								href="{{ $hasEmailAddress ? 'mailto:' . $primaryEmailAddress : '' }}"
								sm
								icon="envelope"
								color="secondary" />
					</div>
					<img
							:src="slide"
							class="w-32 h-32 object-cover"
							alt="Subject image"
					>
				</div>
			</template>

			<!-- Navigation buttons -->
			<button
					@click="prevSlide"
					class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 bg-opacity-50 text-white p-1 rounded-r focus:outline-none transition-opacity duration-200"
					x-show="slides.length > 1 && isHovering"
					x-transition:enter="transition ease-out duration-200"
					x-transition:enter-start="opacity-0"
					x-transition:enter-end="opacity-100"
					x-transition:leave="transition ease-in duration-200"
					x-transition:leave-start="opacity-100"
					x-transition:leave-end="opacity-0"
			>
				<svg
						xmlns="http://www.w3.org/2000/svg"
						class="h-4 w-4"
						fill="none"
						viewBox="0 0 24 24"
						stroke="currentColor">
					<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M15 19l-7-7 7-7" />
				</svg>
			</button>
			<button
					@click="nextSlide"
					class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 bg-opacity-50 text-white p-1 rounded-l focus:outline-none transition-opacity duration-200"
					x-show="slides.length > 1 && isHovering"
					x-transition:enter="transition ease-out duration-200"
					x-transition:enter-start="opacity-0"
					x-transition:enter-end="opacity-100"
					x-transition:leave="transition ease-in duration-200"
					x-transition:leave-start="opacity-100"
					x-transition:leave-end="opacity-0"
			>
				<svg
						xmlns="http://www.w3.org/2000/svg"
						class="h-4 w-4"
						fill="none"
						viewBox="0 0 24 24"
						stroke="currentColor">
					<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M9 5l7 7-7 7" />
				</svg>
			</button>

			<!-- Indicators -->
			<div
					class="absolute bottom-1 left-0 right-0 flex justify-center space-x-1 transition-opacity duration-200"
					x-show="slides.length > 1 && isHovering"
					x-transition:enter="transition ease-out duration-200"
					x-transition:enter-start="opacity-0"
					x-transition:enter-end="opacity-100"
					x-transition:leave="transition ease-in duration-200"
					x-transition:leave-start="opacity-100"
					x-transition:leave-end="opacity-0"
			>
				<template
						x-for="(slide, index) in slides"
						:key="index">
					<button
							@click="activeSlide = index"
							:class="{'bg-white': activeSlide === index, 'bg-gray-300': activeSlide !== index}"
							class="h-1.5 w-1.5 rounded-full focus:outline-none"
					></button>
				</template>
			</div>
		</div>
	</div>
	<div class="space-y-3">
		<h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200 uppercase tracking-wide">Basic</h3>
		<div class="space-y-2">
			<p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-dark-700 px-3 py-1 rounded-md">
				{{ $primarySubject->name }}
			</p>
			<p class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-dark-700 px-3 py-1 rounded-md">
				{{ $primarySubject->subjectAge() }} Year old {{ $primarySubject->gender ?? 'UNK Gender' }}
			</p>
			<p class="text-xs text-primary-600 dark:text-gray-200 bg-gray-100 dark:bg-blue-700 px-3 py-1 rounded-md">
				{{ $primaryPhoneNumber }}
			</p>
		</div>
	</div>
	<div class="space-y-3">
		<h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200 uppercase tracking-wide">Aliases</h3>
		<div class="space-y-2">
			@if($primarySubject && is_array($primarySubject->alias))
				@foreach($primarySubject->alias as $alias)
					<p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-dark-700 px-3 py-1 rounded-md">
						{{ $alias }}
					</p>
				@endforeach
			@else
				<p class="text-sm text-gray-500 dark:text-gray-400 italic">None</p>
			@endif
		</div>
	</div>
	<div class="space-y-3">
		<h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200 uppercase tracking-wide">Risk Factors</h3>
		<div class="space-y-2">
			@if($primarySubject && is_array($primarySubject->risk_factors))
				@foreach($primarySubject->risk_factors as $riskFactor)
					<p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-dark-700 px-3 py-1 rounded-md">
						{{ $riskFactor }}
					</p>
				@endforeach
			@else
				<p class="text-sm text-gray-500 dark:text-gray-400 italic">None</p>
			@endif
		</div>
	</div>
	<div class="space-y-3">
		<h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200 uppercase tracking-wide">Moods</h3>
		<div class="mt-2">
			@php
				$width = 260; // px
				$height = 60; // px
				$pad = 4; // px
				$maxPoints = 20;
				$items = ($recentMoods ?? collect())->sortBy('created_at')->take($maxPoints)->values();
				$count = $items->count();
				$points = '';
				$yFor = function(int $value) use ($height, $pad) {
					$min = 1; $max = 11; // MoodLevels range
					$norm = ($value - $min) / max(($max - $min), 1); // 0..1
					return ($height - $pad) - $norm * ($height - 2 * $pad);
				};
				if ($count >= 2) {
					$xStep = ($width - 2 * $pad) / max(($count - 1), 1);
					for ($i = 0; $i < $count; $i++) {
						$log = $items[$i];
						$x = $pad + $i * $xStep;
						$y = $yFor($log->mood_level->value);
						$points .= ($i === 0 ? '' : ' ') . round($x, 2) . ',' . round($y, 2);
					}
				}
			@endphp

			<div class="bg-gray-50 dark:bg-dark-700 rounded-md p-2">
				@if($count >= 2)
					<svg
							viewBox="0 0 {{ $width }} {{ $height }}"
							width="100%"
							height="70"
							preserveAspectRatio="none"
							aria-label="Mood trend">
						<!-- Baseline grid lines -->
						<line
								x1="0"
								y1="{{ $yFor(6) }}"
								x2="{{ $width }}"
								y2="{{ $yFor(6) }}"
								stroke="currentColor"
								class="text-gray-200 dark:text-dark-500"
								stroke-dasharray="2,3" />
						<!-- Trend polyline -->
						<polyline
								points="{{ $points }}"
								fill="none"
								stroke="currentColor"
								class="text-pink-500 dark:text-pink-400"
								stroke-width="2"
								stroke-linejoin="round"
								stroke-linecap="round" />
						@for($i = 0; $i < $count; $i++)
							@php
								$log = $items[$i];
								$xStep = ($width - 2 * $pad) / max(($count - 1), 1);
								$x = $pad + $i * $xStep;
								$y = $yFor($log->mood_level->value);
							@endphp
							<circle
									cx="{{ $x }}"
									cy="{{ $y }}"
									r="2.2"
									fill="currentColor"
									class="text-blue-500 dark:text-blue-400">
								<title>{{ $log->mood_level->label() }}
									• {{ $log->created_at->format('M d, H:i') }}</title>
							</circle>
						@endfor
					</svg>
				@else
					@php
						// Render a neutral placeholder line when no/insufficient data
						$neutralY = $yFor(6);
						$placeholder = $pad . ',' . $neutralY . ' ' . ($width - $pad) . ',' . $neutralY;
					@endphp
					<svg
							viewBox="0 0 {{ $width }} {{ $height }}"
							width="100%"
							height="70"
							preserveAspectRatio="none"
							aria-label="Mood trend placeholder">
						<polyline
								points="{{ $placeholder }}"
								fill="none"
								stroke="currentColor"
								class="text-gray-300 dark:text-dark-500"
								stroke-width="2" />
					</svg>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Not enough mood data yet.</p>
				@endif
			</div>
		</div>
	</div>
	<div class="flex justify-end items-start">
		<x-dropdown
				icon="ellipsis-vertical"
				class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-700 rounded-full p-1"
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
