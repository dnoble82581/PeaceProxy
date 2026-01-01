<?php

	use App\Enums\Subject\MoodLevels;
	use App\Factories\MessageFactory;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Models\User;
	use App\Services\ContactPoint\ContactPointFetchingService;
	use App\Support\EventNames\SubjectEventNames;
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
		public bool $showEditImagesModal = false;

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

		/**
		 * Format the most recent 5 moods for a compact chart.
		 * Returns: ['categories' => string[], 'data' => int[]]
		 */
		public function formatRecentMoodsForChart():array
		{
			$logs = collect($this->recentMoods ?? [])
				->sortByDesc('created_at')
				->take(5)
				->reverse()
				->values();

			$categories = $logs->map(function ($log) {
				try {
					$tz = authUser()->timezone ?? config('app.timezone', 'UTC');
					return $log->created_at->setTimezone($tz)->format('H:i');
				} catch (Throwable $e) {
					return optional($log->created_at)->format('H:i') ?? '';
				}
			})->toArray();

			$data = $logs->map(function ($log) {
				return (int) ($log->mood_level->value ?? 0);
			})->toArray();

			return [
				'categories' => $categories,
				'data' => $data,
			];
		}

		public function loadImageUrls():void
		{
			$this->imageUrls = [];               // now an array of objects
			$seen = [];                          // prevent duplicates by URL
			$limit = 5;

			$primaryImageUrl = $this->primarySubject->primaryImage(); // the current method returns URL
			$temporaryUrl = $this->primarySubject->temporaryImageUrl();

			// Helper to push an item safely
			$push = function (array $item) use (&$seen) {
				// de-dupe by canonical url (without query)
				$key = parse_url($item['src'], PHP_URL_SCHEME)
					? $item['src']
					: ('//'.ltrim($item['src'], '/'));
				if (isset($seen[$key])) return;
				$seen[$key] = true;
				$this->imageUrls[] = $item;
			};

			// 1) Add primary first (if real, not temp)
			if ($primaryImageUrl && $primaryImageUrl !== $temporaryUrl) {
				// Try to find the primary record in the collection for metadata
				$primaryModel = $this->primarySubject->images
					->first(fn($img) => ($img->src ?? (method_exists($img,
							'src')? $img->url() : null)) === $primaryImageUrl);

				$ts = $primaryModel?->updated_at?->getTimestamp() ?? now()->getTimestamp();

				$push([
					'id' => $primaryModel?->id,
					'src' => $primaryImageUrl,
					'thumb' => $primaryModel->thumb_url ?? null,
					'caption' => $primaryModel->caption ?? null,
					'updated_at' => $ts,
					'uploader' => [
						'id' => $primaryModel?->user_id,
						'name' => optional($primaryModel?->user)->name,
					],
					'is_primary' => true,
					'is_placeholder' => false,
					'ver' => $ts,
					'key' => sprintf('img-%s-%s', $primaryModel?->id ?? 'primary', $ts),
				]);
			}

			// 2) Add other images until limit
			foreach ($this->primarySubject->images as $image) {
				if (count($this->imageUrls) >= $limit) break;

				$imageUrl = $image->url ?? (method_exists($image, 'url')? $image->url() : null);
				if (!$imageUrl || $imageUrl === $primaryImageUrl) continue;

				$ts = $image->updated_at?->getTimestamp() ?? now()->getTimestamp();

				$push([
					'id' => $image->id,
					'src' => $imageUrl,
					'thumb' => $image->thumb_url ?? null,
					'caption' => $image->caption ?? null,
					'updated_at' => $ts,
					'uploader' => [
						'id' => $image->user_id ?? null,
						'name' => optional($image->user ?? null)->name,
					],
					'is_primary' => false,
					'is_placeholder' => false,
					'ver' => $ts,
					'key' => sprintf('img-%s-%s', $image->id ?? 'x', $ts),
				]);
			}

			// 3) Fallback to temporary placeholder if none added
			if (empty($this->imageUrls) && $temporaryUrl) {
				$ts = now()->getTimestamp();
				$push([
					'id' => null,
					'src' => $temporaryUrl,
					'thumb' => null,
					'caption' => null,
					'updated_at' => null,
					'uploader' => ['id' => null, 'name' => null],
					'is_primary' => false,
					'is_placeholder' => true,
					'ver' => $ts,
					'key' => sprintf('img-temp-%s', $ts),
				]);
			}

			// Hard cap to $limit just in case
			if (count($this->imageUrls) > $limit) {
				$this->imageUrls = array_slice($this->imageUrls, 0, $limit);
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
			} catch (Exception $e) {
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

		public function editSubjectImages()
		{
			$this->showEditImagesModal = true;
		}

		public function getListeners():array
		{
			$subjectId = $this->primarySubject?->id;

			if (!$subjectId) {
				return [];
			}
			return
				[
					'echo-private:'.\App\Support\Channels\Subject::subjectMood($subjectId).',.'.SubjectEventNames::MOOD_CREATED => 'handleMoodCreated',
					'echo-private:'.\App\Support\Channels\Subject::subject($subjectId).',.'.SubjectEventNames::SUBJECT_UPDATED => 'handleSubjectUpdated',
					'echo-private:'.\App\Support\Channels\Subject::subject($subjectId).',.'.SubjectEventNames::CONTACT_DELETED => 'handleContactDeleted',
				];
		}

		public function handleSubjectUpdated(array $event):void
		{
			// Rebuild slides (this method already resets the array)
			$this->loadImageUrls();
			$this->loadPrimaryPhoneNumber();


			$messageFactory = app(MessageFactory::class);
			$message = $messageFactory->generateMessage($this->primarySubject, 'SubjectEdited');
			$this->toast()->timeout()->info($message)->send();

		}

		public function handleContactDeleted(array $event):void
		{
			logger($event);
		}

		public function handleMoodCreated($event):void
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

		protected function sendToastToThisUser($newMoodLabel, $newMoodIcon):void
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
		protected function getUserById(int $userId):?User
		{
			return User::find($userId);
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
    slides: @entangle('imageUrls'),
    nextSlide() {
      if (!this.slides.length) return;
      this.activeSlide = (this.activeSlide + 1) % this.slides.length;
    },
    prevSlide() {
      if (!this.slides.length) return;
      this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
    }
  }"
			class="relative w-32 h-32"
			@mouseenter="isHovering = true"
			@mouseleave="isHovering = false"
	>
		<div class="overflow-hidden rounded-lg w-full h-full relative shadow-md">
			<template
					x-for="(slide, idx) in slides"
					:key="slide.key ?? idx">
				<div
						x-show="activeSlide === idx"
						x-transition:enter="transition ease-out duration-300"
						x-transition:enter-start="opacity-0 transform scale-90"
						x-transition:enter-end="opacity-100 transform scale-100"
						x-transition:leave="transition ease-in duration-300"
						x-transition:leave-start="opacity-100 transform scale-100"
						x-transition:leave-end="opacity-0 transform scale-90"
						class="absolute inset-0"
				>
					<img
							class="w-full h-full object-cover"
							:src="(slide.src ?? slide)"
							:alt="slide.caption || ('Image ' + (slide.id ?? ''))"
					>

					<div
							class="absolute flex justify-between w-full bottom-1 left-0 px-2"
							x-show="isHovering">
						<x-button.circle
								@disabled(!$hasPhoneNumber)
								wire:click="openPhoneIntegrationModal"
								sm
								icon="phone"
								color="primary" />
						<x-button.circle
								@disabled(!$hasEmailAddress)
								@if($hasEmailAddress) href="mailto:{{ $primaryEmailAddress }}"
								@endif
								sm
								icon="envelope"
								color="secondary" />
					</div>
				</div>
			</template>
			<div
					x-transition.opacity
					x-show="isHovering"
					class="absolute top-0 right-0 transition-opacity duration-200">
				<x-button
						wire:click="editSubjectImages"
						xs
						text="Edit"
				/>
			</div>
			<!-- Nav -->
			<button
					@click="prevSlide"
					class="absolute left-0 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-1 rounded-r transition-opacity duration-200"
					x-show="slides.length > 1 && isHovering"
					x-transition.opacity>
				<x-icon
						name="chevron-left"
						class="w-4 h-4" />
				<!-- left chevron SVG -->
			</button>
			<button
					@click="nextSlide"
					class="absolute right-0 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-1 rounded-l transition-opacity duration-200"
					x-show="slides.length > 1 && isHovering"
					x-transition.opacity>
				<x-icon
						name="chevron-right"
						class="w-4 h-4" />
				<!-- right chevron SVG -->
			</button>

			<!-- Dots -->
			<div
					class="absolute bottom-1 left-0 right-0 flex justify-center space-x-1 transition-opacity duration-200"
					x-show="slides.length > 1 && isHovering"
					x-transition.opacity>
				<template
						x-for="(slide, idx) in slides"
						:key="idx">
					<button
							@click="activeSlide = idx"
							:class="{'bg-white': activeSlide === idx, 'bg-gray-300': activeSlide !== idx}"
							class="h-1.5 w-1.5 rounded-full"></button>
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
			@php    $pn = $primaryPhoneNumber ?? '';
				$digits = preg_replace('/\D+/', '', (string) $pn);
				$formattedPhone = $pn;
				if (strlen($digits) === 10) {
					$formattedPhone = '(' . substr($digits, 0, 3) . ')-' . substr($digits, 3, 3) . '-' . substr($digits, 6, 4);
				} elseif (strlen($digits) === 11 && $digits[0] === '1') {
					// Strip leading country code 1 and format as US number
					$formattedPhone = '(' . substr($digits, 1, 3) . ')-' . substr($digits, 4, 3) . '-' . substr($digits, 7, 4);
				}
			@endphp
			<p class="text-xs text-primary-600 dark:text-gray-200 bg-gray-100 dark:bg-blue-700 px-3 py-1 rounded-md">
				{{ $formattedPhone }}
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
		<div class="flex items-center gap-2">
			<h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200 uppercase tracking-wide">Risk Factors</h3>
		</div>

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
		<div
				class="mt-2 w-48">
			<div
					class="bg-gray-50 dark:bg-dark-700 rounded-md p-2"
					x-data="subjectMoodSpark(@js($this->formatRecentMoodsForChart()))"
					x-init="init()"
					@theme-changed.window="onThemeChanged($event.detail.theme)"
					@mood-logs-updated.window="onMoodLogsUpdated($event.detail)"
			>
				<div class="spark w-full h-20"></div>
				@if(($recentMoods ?? collect())->count() === 0)
					<p class="mt-2 text-xs text-gray-500 dark:text-gray-400 italic">No moods recorded</p>
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
			<x-dropdown.submenu
					text="Quick Actions"
					position="left-start">
				<x-dropdown.items
						wire:click="editSubjectImages"
						text="Edit Images"
						icon="photo" />
				<x-dropdown.items
						text="Edit Basic"
						icon="finger-print" />
				<x-dropdown.items
						text="Edit Aliases"
						icon="identification" />
				<x-dropdown.items
						text="Edit Risks"
						icon="exclamation-circle" />
			</x-dropdown.submenu>
		</x-dropdown>
	</div>
	<x-slide
			title="Edit Images"
			wire="showEditImagesModal">
		<livewire:forms.subject.edit-subject-images :subjectId="$primarySubject->id" />
	</x-slide>
</div>

@push('scripts')
	<!-- ApexCharts CDN (loaded once by the browser cache across pushes) -->
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script>
		function subjectMoodSpark (initial = { categories: [], data: [] }) {
			return {
				chart: null,
				dark: document.documentElement.classList.contains('dark'),
				data: initial,

				init () {
					this.render()
					window.addEventListener('theme-changed', (e) => this.onThemeChanged(e.detail?.theme))
				},

				render () {
					const el = this.$root.querySelector('.spark')
					if (!el || typeof ApexCharts === 'undefined') { return }

					const categories = (this.data?.categories?.length ? this.data.categories : [''])
					const seriesData = (this.data?.data?.length ? this.data.data : [0])

					const opts = {
						chart: {
							type: 'line',
							height: 80,
							sparkline: { enabled: true },
							animations: { enabled: true }
						},
						stroke: { curve: 'smooth', width: 2 },
						dataLabels: { enabled: false },
						tooltip: {
							enabled: true,
							theme: this.dark ? 'dark' : 'light',
							style: {
								fontSize: '12px',
								fontFamily: 'inherit',
							},
							custom: function ({ series, seriesIndex, dataPointIndex, w }) {
								const isDark = w.config.tooltip.theme === 'dark'
								const bgColor = isDark ? '#374151' : '#F3F4F6' // Dark: gray-700, Light: gray-100
								const textColor = isDark ? '#F9FAFB' : '#1F2937' // Dark: gray-50, Light: gray-800
								const borderColor = isDark ? '#4B5563' : '#D1D5DB' // Dark: gray-600, Light: gray-300

								const value = series[seriesIndex][dataPointIndex]
								const v = Math.round(value ?? 0)
								const labels = {
									1: 'Suicidal',
									2: 'Severely Depressed',
									3: 'Depressed',
									4: 'Sad',
									5: 'Low',
									6: 'Neutral',
									7: 'Slightly Happy',
									8: 'Happy',
									9: 'Euphoric',
									10: 'Hypomanic',
									11: 'Manic',
								}
								const moodLabel = labels[v] ?? String(value)

								return '<div class="custom-tooltip" style="' +
									'background: ' + bgColor + '; ' +
									'color: ' + textColor + '; ' +
									'border: 1px solid ' + borderColor + '; ' +
									'padding: 6px 8px; ' +
									'border-radius: 4px; ' +
									'box-shadow: 0 2px 5px rgba(0,0,0,0.15); ' +
									'font-size: 12px;">' + moodLabel + '</div>'
							}
						},
						series: [{ name: 'Mood', data: seriesData }],
						xaxis: { categories },
						yaxis: { min: 1, max: 11, tickAmount: 2 },
						colors: [this.dark ? '#60A5FA' : '#2563EB']
					}

					if (this.chart) {
						try {
							this.chart.updateOptions({ xaxis: { categories }, series: [{ data: seriesData }] })
							return
						} catch (e) {
							try { this.chart.destroy() } catch {}
							this.chart = null
						}
					}

					this.chart = new ApexCharts(el, opts)
					this.chart.render()
				},

				onThemeChanged (theme) {
					this.dark = theme === 'dark' || document.documentElement.classList.contains('dark')
					if (this.chart) {
						try { this.chart.updateOptions({ tooltip: { theme: this.dark ? 'dark' : 'light' } }) } catch {}
					}
				},

				onMoodLogsUpdated (payload) {
					if (!payload || !Array.isArray(payload.data) || !Array.isArray(payload.categories)) { return }
					const last5 = payload.data.slice(-5)
					const last5Cats = payload.categories.slice(-5)
					this.data = { categories: last5Cats, data: last5 }
					this.render()
				}
			}
		}
	</script>
@endpush
