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
			<div class="grid grid-cols-1 gap-2">
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
			<div>
				<span class="font-semibold text-gray-700 dark:text-dark-200">Armed:</span> {{ $armedStatus }}
			</div>
			<div>
				@if(count($firstTwoPhones) > 0)
					<div class="text-sm text-gray-900 dark:text-dark-50">
						@foreach($firstTwoPhones as $phone)
							<p>{{ $phone }}</p>
						@endforeach
					</div>
				@else
					<div class="text-sm text-gray-400">No phone numbers found.</div>
				@endif
			</div>
		</div>
	</div>
</x-card>
