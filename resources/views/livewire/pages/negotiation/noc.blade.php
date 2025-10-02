<?php

	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\ContactPoint\ContactPointFetchingService;
	use Illuminate\View\View;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\On;
	use Livewire\Attributes\Title;
	use Livewire\Volt\Component;
	use TallStackUi\Traits\Interactions;


	new #[Layout('layouts.negotiation'), Title('NOC - Peace Proxy')] class extends Component {

		public ?Negotiation $negotiation = null;
		public bool $showPhoneModal = false;
		public ?Subject $primarySubject = null;
		/** @var array<int, array{label:string, number:string|null, ext:string, isPrimary:string, fullDisplay:string}> */
		public array $phoneNumbers = [];
		use Interactions;


		public function mount(Negotiation $negotiation):void
		{
			$this->negotiation = $negotiation;
			$this->primarySubject = $negotiation->primarySubject();
			$this->loadPhoneNumbers();
		}

		public function rendering(View $view):void
		{
			$view->layoutData(['negotiation' => $this->negotiation]);
		}

		#[On('togglePhoneModal')]
		public function toggleShowPhoneModal():void
		{
			$this->showPhoneModal = !$this->showPhoneModal;
		}

		/**
		 * Load all phone numbers for the primary subject.
		 */
		public function loadPhoneNumbers():void
		{
			if ($this->primarySubject === null) {
				$this->phoneNumbers = [];
				return;
			}

			$contactPoints = app(ContactPointFetchingService::class)
				->getContactPointsBySubject($this->primarySubject);

			$this->phoneNumbers = $contactPoints
				->filter(static function ($contactPoint):bool {
					return $contactPoint->kind === 'phone' && $contactPoint->phone !== null;
				})
				->map(static function ($contactPoint):array {
					$label = $contactPoint->label?: 'Phone';
					$number = $contactPoint->phone?->e164;
					$ext = $contactPoint->phone?->ext? ' ext. '.$contactPoint->phone->ext : '';
					$isPrimary = $contactPoint->is_primary? ' (Primary)' : '';

					return [
						'label' => $label,
						'number' => $number,
						'ext' => $ext,
						'isPrimary' => $isPrimary,
						'fullDisplay' => "$label: $number$ext$isPrimary",
					];
				})
				->values()
				->all();
		}
	}

?>

<div class="text-white px-8 mb-16">
	<livewire:pages.negotiation.noc-elements.top-cards :negotiation="$this->negotiation" />
	<livewire:pages.negotiation.noc-elements.notifications />
	<div class="grid grid-cols-1 md:grid-cols-8 gap-4 mt-4">
		<div class="col-span-3 h-[calc(100vh-10rem)]">
			<livewire:pages.negotiation.chat.negotiation-chat :negotiationId="$this->negotiation->id" />
		</div>
		<div class="col-span-5 h-[calc(100vh-10rem)]">
			<livewire:pages.negotiation.board.negotiation-board :negotiationId="$this->negotiation->id" />
		</div>
	</div>
	<x-modal
			center
			name="phone-integration-modal"
			wire="showPhoneModal">
		<x-slot:title>
			Subject Phone Numbers
		</x-slot:title>
		<div class="p-4">
			@if(count($phoneNumbers) > 0)
				<div class="space-y-2">
					@foreach($phoneNumbers as $index => $phone)
						<div
								class="flex items-center gap-2"
								wire:key="phone-{{ $index }}">
							<x-icon
									class="w-6 h-6 text-primary-500 flex-shrink-0"
									name="phone" />
							<code class="block bg-gray-100 dark:bg-dark-700 px-3 py-2 rounded-md w-full">
								{{ $phone['fullDisplay'] }}
							</code>
						</div>
					@endforeach
				</div>
			@else
				<div class="flex items-center gap-2">
					<x-icon
							class="w-8 h-8 text-yellow-500"
							name="exclamation-triangle" />
					<p class="text-gray-600 dark:text-dark-300">
						No phone numbers found for this subject.
					</p>
				</div>
			@endif
		</div>
	</x-modal>
</div>
