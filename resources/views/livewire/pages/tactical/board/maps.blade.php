<?php

	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Enums\Subject\SubjectNegotiationRoles;
	use App\Services\Map\MapsStaticService;
	use App\Services\Map\GeocodeService;
	use Livewire\Volt\Component;

	new class extends Component {
		public ?Negotiation $negotiation = null;
		public array $resources = [];
		public float $lat;
		public float $lng;
		public string $search = '';

		public function mount(Negotiation $negotiation):void
		{
			$this->negotiation = $negotiation;

			$lat = $negotiation->latitude;
			$lng = $negotiation->longitude;

			if ($lat === null || $lng === null || (float) $lat === 0.0 || (float) $lng === 0.0) {
				$coords = app(GeocodeService::class)->geocode('511 S Capitol St, Iowa City, IA 52240');
				if ($coords !== null) {
					$this->lat = (float) $coords['lat'];
					$this->lng = (float) $coords['lng'];
				} else {
					$this->lat = 41.6611; // Fallback to Iowa City center if geocoding is unavailable
					$this->lng = -91.5302;
				}
			} else {
				$this->lat = (float) $lat;
				$this->lng = (float) $lng;
			}

			$this->resources = $negotiation->resources()
				->orderBy('name')
				->get(['id', 'name', 'type', 'latitude', 'longitude'])
				->toArray();
		}

		public function searchAddress():void
		{
			$this->search = trim($this->search);
			if ($this->search === '') {
				return;
			}

			$coords = app(GeocodeService::class)->geocode($this->search);
			if ($coords === null) {
				return;
			}

			$this->lat = (float) $coords['lat'];
			$this->lng = (float) $coords['lng'];

			$this->dispatch('map-center-updated', lat: $this->lat, lng: $this->lng);
		}
	};

?>

<div class="w-[40rem] h-[40rem]">
	<gmp-map
			center="40.12150192260742,-100.45039367675781"
			zoom="4"
			map-id="DEMO_MAP_ID">
		<gmp-advanced-marker
				position="40.12150192260742,-100.45039367675781"
				title="My location"></gmp-advanced-marker>
	</gmp-map>
</div>
