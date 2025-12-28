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

<div class="">
	<div
			id="text-input-card"
			class="w-2xl flex p-2 gap-4">
		<div class="flex-1">
			<x-input
					type="text"
					id="text-input"
					wire:model.live="search"
					placeholder="Search for a place"
					wire:keydown.enter="searchAddress"
			/>
		</div>

		<x-button
				id="text-input-button"
				wire:click="searchAddress"
				text="Search" />

	</div>
	<div class="flex gap-4">
		<div
				id="map"
				wire:ignore
				class="h-[40rem] w-[40rem] flex-1 basis-0 min-w-0"></div>
		<div class="w-80 shrink-0">
			Resources
		</div>
	</div>
	<div>
		<p>{{ $negotiation->address }}</p>
	</div>

</div>

@once
	@if (blank(config('services.maps.js_key')))
		<div class="rounded border border-red-300 bg-red-50 text-red-800 p-3 text-sm">
			Google Maps API key is not configured. Please set GOOGLE_MAPS_API_KEY in your .env and reload the page.
		</div>
	@else
		<script>(g => {
				var h, a, k, p = 'The Google Maps JavaScript API', c = 'google', l = 'importLibrary', q = '__ib__',
					m = document, b = window
				b = b[c] || (b[c] = {})
				var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams,
					u = () => h || (h = new Promise(async (f, n) => {
						await (a = m.createElement('script'))
						e.set('libraries', [...r] + '')
						for (k in g) e.set(k.replace(/[A-Z]/g, t => '_' + t[0].toLowerCase()), g[k])
						e.set('callback', c + '.maps.' + q)
						a.src = `https://maps.${c}apis.com/maps/api/js?` + e
						d[q] = f
						a.onerror = () => h = n(Error(p + ' could not load.'))
						a.nonce = m.querySelector('script[nonce]')?.nonce || ''
						m.head.append(a)
					}))

				d[l] ? console.warn(p + ' only loads once. Ignoring:', g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
			})
			({ key: "{{ config('services.maps.js_key') }}", v: 'weekly' })</script>

		<script>
			(function () {
				let map = null
				let marker = null
				let AdvancedMarkerElement = null

				const init = async () => {
					try {
						const { Map } = await google.maps.importLibrary('maps')
						try {
							({ AdvancedMarkerElement } = await google.maps.importLibrary('marker'))
						} catch (err) {
							console.warn('AdvancedMarkerElement not available, falling back to standard Marker.', err)
						}

						const center = { lat: @json($lat ?? 0), lng: @json($lng ?? 0) }
						const mapEl = document.getElementById('map')
						if (!mapEl) { return }

						const configuredMapId = '{{ config('services.maps.map_id') }}'
						map = new Map(mapEl, {
							center: center,
							zoom: 14,
							mapId: configuredMapId || undefined,
						})

						if (AdvancedMarkerElement && configuredMapId) {
							marker = new AdvancedMarkerElement({ map, position: center })
						} else {
							marker = new google.maps.Marker({ map, position: center })
						}

						window.addEventListener('map-center-updated', (event) => {
							const { lat, lng } = event.detail || {}
							if (lat == null || lng == null) { return }
							const pos = { lat: parseFloat(lat), lng: parseFloat(lng) }
							if (map) { map.setCenter(pos) }
							if (marker) { marker.setPosition(pos) }
						})
					} catch (e) {
						console.error('Failed to initialize Google Map:', e)
					}
				}

				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', init)
				} else {
					init()
				}
			})()
		</script>
	@endif
@endonce