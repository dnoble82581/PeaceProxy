<?php

	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\ContactPoint\ContactPointFetchingService;
	use Livewire\Volt\Component;

	new class extends Component {
		public Negotiation $negotiation;
		public ?Subject $primarySubject = null;

		public array $imageUrls = [];
		public array $firstTwoPhones = [];
		public string $armedStatus = 'Unknown';

		public function mount($negotiation):void
		{
			$this->negotiation = $negotiation;
			$this->primarySubject = $negotiation?->primarySubject();
			$this->prepareImages();
			$this->preparePhones();
			$this->armedStatus = $this->determineArmedStatus();
		}

		public function prepareImages():void
		{
			$this->imageUrls = [];
			if (!$this->primarySubject) return;

			$primaryImageUrl = $this->primarySubject->primaryImage();
			if ($primaryImageUrl && $primaryImageUrl !== $this->primarySubject->temporaryImageUrl()) {
				$this->imageUrls[] = $primaryImageUrl;
			}
			foreach ($this->primarySubject->images as $image) {
				if (count($this->imageUrls) >= 5) break;
				$imageUrl = $image->url ?? (method_exists($image, 'url')? $image->url() : null);
				if ($imageUrl && $imageUrl !== $primaryImageUrl) {
					$this->imageUrls[] = $imageUrl;
				}
			}
			if (empty($this->imageUrls)) {
				$this->imageUrls[] = $this->primarySubject->temporaryImageUrl();
			}
		}

		public function preparePhones():void
		{
			$this->firstTwoPhones = [];
			if (!$this->primarySubject) return;

			$contactPoints = app(ContactPointFetchingService::class)->getContactPointsBySubject($this->primarySubject);
			$phones = $contactPoints->filter(fn($cp) => $cp->kind === 'phone');
			$primaryPhones = $phones->where('is_primary', true)->values();
			$nonPrimaryPhones = $phones->where('is_primary', false)->values();
			$combined = $primaryPhones->concat($nonPrimaryPhones)->take(2);
			foreach ($combined as $cp) {
				if ($cp->phone) {
					$label = $cp->label?: 'Phone';
					$num = $cp->phone->e164;
					$ext = $cp->phone->ext? (' ext. '.$cp->phone->ext) : '';
					$this->firstTwoPhones[] = $label.': '.$num.$ext;
				}
			}
		}

		public function getListeners()
		{
			return [
				'echo-private:'.\App\Support\Channels\Subject::subjectMood($this->primarySubject->id).',.'.\App\Support\EventNames\SubjectEventNames::MOOD_CREATED => 'handleMoodCreated',
				'echo-private:'.\App\Support\Channels\Subject::subject($this->primarySubject->id).',.'.\App\Support\EventNames\SubjectEventNames::SUBJECT_UPDATED => 'handleSubjectUpdated',
				'echo-private:'.\App\Support\Channels\Subject::subject($this->primarySubject->id).',.'.\App\Support\EventNames\SubjectEventNames::CONTACT_DELETED => 'handleContactDeleted',
			];
		}

		public function handleSubjectUpdated()
		{
			$this->prepareImages();
		}

		private function determineArmedStatus():string
		{
			try {
				$subject = $this->primarySubject;
				if (!$subject) return 'Unknown';
				$attrs = $subject->getAttributes();
				if (array_key_exists('is_armed', $attrs)) {
					return $attrs['is_armed']? 'Yes' : 'No';
				}
				if (array_key_exists('armed', $attrs)) {
					return (bool) $attrs['armed']? 'Yes' : 'No';
				}
				$risk = $subject->risk_factors ?? null;
				if (is_array($risk)) {
					if ((isset($risk['armed']) && (bool) $risk['armed'] === true) || in_array('armed',
							array_map('strtolower', array_keys($risk)))) {
						return 'Yes';
					}
					if (in_array('unarmed', array_map('strtolower', array_keys($risk)))) {
						return 'No';
					}
					if (in_array('armed', array_map('strtolower', $risk), true)) {
						return 'Yes';
					}
				}
				$tags = $this->negotiation->tags ?? [];
				if (is_array($tags) && in_array('armed', array_map('strtolower', $tags), true)) {
					return 'Yes';
				}
			} catch (\Throwable $e) {
			}
			return 'Unknown';
		}
	};

?>

<x-card
		class="h-[15rem] overflow-hidden"
		header="Target">
	<div class="flex gap-4">
		<div class="h-full flex">
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
				<div class="overflow-hidden rounded-md w-32 h-32 relative border border-gray-200 dark:border-dark-500">
					<template x-for="(s, i) in slides" x-bind:key="s.key ?? i">
						<div
							x-show="activeSlide === i"
							x-transition:enter="transition ease-out duration-300"
							x-transition:enter-start="opacity-0 transform scale-90"
							x-transition:enter-end="opacity-100 transform scale-100"
							x-transition:leave="transition ease-in duration-300"
							x-transition:leave-start="opacity-100 transform scale-100"
							x-transition:leave-end="opacity-0 transform scale-90"
							class="absolute inset-0"
						>
							<img class="w-full h-full object-cover" x-bind:src="(s.src ?? s)" x-bind:alt="s.caption ?? (`Image ${s.id ?? ''}`)">
						</div>
					</template>

					<button @click="prevSlide" class="absolute left-0 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-1 rounded-r transition-opacity duration-200" x-show="slides.length > 1 && isHovering" x-transition.opacity>
						<x-icon name="chevron-left" class="w-4 h-4" />
					</button>
					<button @click="nextSlide" class="absolute right-0 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-1 rounded-l transition-opacity duration-200" x-show="slides.length > 1 && isHovering" x-transition.opacity>
						<x-icon name="chevron-right" class="w-4 h-4" />
					</button>

					<div class="absolute bottom-1 left-1/2 -translate-x-1/2 flex gap-1" x-show="slides.length > 1">
						<template x-for="(s, i) in slides" x-bind:key="'dot-'+(s.key ?? i)">
							<button @click="activeSlide = i" x-bind:class="{'bg-white': activeSlide === i, 'bg-white/50 hover:bg-white/80': activeSlide !== i}" class="w-1.5 h-1.5 rounded-full transition-colors"></button>
						</template>
					</div>
				</div>
			</div>
		</div>
		<div class="text-sm text-gray-900 dark:text-dark-50 space-y-1">
			<div>
				<span class="font-semibold text-gray-700 dark:text-dark-200">Name:</span> {{ $primarySubject->name ?? 'Unknown' }}
			</div>
			<div>
				<span class="font-semibold text-gray-700 dark:text-dark-200">Age:</span> {{ ($primarySubject && method_exists($primarySubject, 'subjectAge') && $primarySubject->subjectAge() > 0) ? $primarySubject->subjectAge() : 'Unknown' }}
			</div>
			<div>
				<span class="font-semibold text-gray-700 dark:text-dark-200">Armed:</span> {{ $armedStatus }}
			</div>
			<div>
				@if(count($firstTwoPhones) > 0)
					<div class="text-sm text-gray-900 dark:text-dark-50">
						@foreach($firstTwoPhones as $phone)
							<p>{{ $phone}}</p>
						@endforeach
					</div>
				@else
					<div class="text-sm text-gray-400">No phone numbers found.</div>
				@endif
			</div>
		</div>
	</div>
</x-card>
