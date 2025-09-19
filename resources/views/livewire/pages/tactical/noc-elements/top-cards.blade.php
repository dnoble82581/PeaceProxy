<?php

	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\ContactPoint\ContactPointFetchingService;
	use Livewire\Volt\Component;

	new class extends Component {
		public Negotiation $negotiation;
		public Subject $primarySubject;

		// Prepared view data
		public array $imageUrls = [];
		public array $firstTwoPhones = [];
		public string $armedStatus = 'Unknown';

		public function mount($negotiation):void
		{
			$this->negotiation = $negotiation;
			$this->primarySubject = $negotiation->primarySubject();

			$this->prepareImages();
			$this->preparePhones();
			$this->armedStatus = $this->determineArmedStatus();
		}

		public function getListeners():array
		{
			$negotiationId = (int) ($this->negotiation->id ?? 0);
			$tenantId = (int) (tenant()->id ?? 0);
			return [

			];
		}

		public function prepareImages():void
		{
			$this->imageUrls = [];
			if (!$this->primarySubject) {
				return;
			}

			$primaryImageUrl = $this->primarySubject->primaryImage();
			if ($primaryImageUrl && $primaryImageUrl !== $this->primarySubject->temporaryImageUrl()) {
				$this->imageUrls[] = $primaryImageUrl;
			}

			foreach ($this->primarySubject->images as $image) {
				if (count($this->imageUrls) >= 5) {
					break;
				}
				$imageUrl = $image->url ?? (method_exists($image, 'url')? $image->url() : null);
				if ($imageUrl && $imageUrl !== $primaryImageUrl) {
					$this->imageUrls[] = $imageUrl;
				}
			}

			if (empty($this->imageUrls)) {
				$this->imageUrls[] = $this->primarySubject->temporaryImageUrl();
			}
		}

		private function preparePhones():void
		{
			$this->firstTwoPhones = [];
			if (!$this->primarySubject) {
				return;
			}

			$contactPoints = app(ContactPointFetchingService::class)->getContactPointsBySubject($this->primarySubject);
			$phones = $contactPoints->filter(fn($cp) => $cp->kind === 'phone');

			// First take primaries, then fill with non-primaries until we have 2
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

		private function determineArmedStatus():string
		{
			try {
				$subject = $this->primarySubject;
				if (!$subject) {
					return 'Unknown';
				}

				// 1) Direct boolean attribute if present
				$attrs = $subject->getAttributes();
				if (array_key_exists('is_armed', $attrs)) {
					return $attrs['is_armed']? 'Yes' : 'No';
				}
				if (array_key_exists('armed', $attrs)) {
					return (bool) $attrs['armed']? 'Yes' : 'No';
				}

				// 2) Risk factors array may contain an "armed" flag or value
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

				// 3) Negotiation tags sometimes include context like 'armed'
				$tags = $this->negotiation->tags ?? [];
				if (is_array($tags) && in_array('armed', array_map('strtolower', $tags), true)) {
					return 'Yes';
				}

			} catch (\Throwable $e) {
				// fall through to Unknown
			}

			return 'Unknown';
		}
	};

?>
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 h-[15rem]">
	<!-- Card 1: Subject Images -->
	<x-card
			class="h-full">
		<x-slot:header>
			<div class="p-4 flex items-center justify-between">
				<h3>Case #: <span class="font-semibold">J25000215</span></h3>
				<x-badge
						text="Unarmed"
						color="teal" />
			</div>
		</x-slot:header>
		<div class="space-y-3">
			<div>
				<div class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-300">Situation</div>
				<div class="text-sm text-gray-900 dark:text-dark-50">{{ $negotiation->summary ?? 'Awaiting more ingormation' }}</div>
			</div>
			<div>
				<div class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-300">Description</div>
				<div class="text-sm text-gray-900 dark:text-dark-50">{{ $negotiation->initial_complaint }}</div>
			</div>
		</div>
	</x-card>

	<!-- Card 2: Location & Description -->
	<x-card
			class="h-full overflow-hidden"
			header="Target">
		<div>

			<div class="flex gap-4">
				<div class="h-full flex">
					<div class="grid grid-cols-3 gap-2">
						@foreach(array_slice($imageUrls, 0, 5) as $idx => $url)
							<img
									src="{{ $url }}"
									alt="Subject Image {{ $idx+1 }}"
									class="w-24 h-24 object-cover rounded-md border border-gray-200 dark:border-dark-500" />
						@endforeach
					</div>
				</div>
				<div class="text-sm text-gray-900 dark:text-dark-50 space-y-1">
					<div>
						<span class="font-semibold text-gray-700 dark:text-dark-200">Name:</span> {{ $primarySubject->name ?? 'Unknown' }}
					</div>
					<div>
						<span class="font-semibold text-gray-700 dark:text-dark-200">Age:</span> {{ ($primarySubject && method_exists($primarySubject, 'subjectAge') && $primarySubject->subjectAge() > 0) ? $primarySubject->subjectAge() : 'Unknown' }}
					</div>
					<div><span class="font-semibold text-gray-700 dark:text-dark-200">Armed:</span> {{ $armedStatus }}
					</div>
				</div>
			</div>
			<div class="mt-2">
				@if(count($firstTwoPhones) > 0)
					<ul class="list-disc list-inside text-sm text-gray-900 dark:text-dark-50">
						@foreach($firstTwoPhones as $phone)
							<li>{{ $phone }}</li>
						@endforeach
					</ul>
				@else
					<div class="text-sm text-gray-400">No phone numbers found.</div>
				@endif
			</div>
		</div>
	</x-card>


	<!-- Card 3: First Two Primary Phone Numbers -->
	<x-card class="h-full">
		<div class="space-y-2">
			<div class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-300">Primary Phone Numbers
			</div>
			@if(count($firstTwoPhones) > 0)
				<ul class="list-disc list-inside text-sm text-gray-900 dark:text-dark-50">
					@foreach($firstTwoPhones as $phone)
						<li>{{ $phone }}</li>
					@endforeach
				</ul>
			@else
				<div class="text-sm text-gray-400">No phone numbers found.</div>
			@endif
		</div>
	</x-card>

	<!-- Card 4: Reserved Placeholder -->
	<x-card class="h-full">
		<div class="h-full flex items-center justify-center text-sm text-gray-500 dark:text-dark-300">
			Reserved
		</div>
	</x-card>
</div>