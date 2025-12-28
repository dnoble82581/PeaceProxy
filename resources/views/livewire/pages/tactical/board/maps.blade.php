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
				let geocoder = null

				function setCenterAndMarker (pos) {
					if (!pos || typeof pos.lat !== 'number' || typeof pos.lng !== 'number') { return }
					if (map) { map.setCenter(pos) }
					try {
						if (marker && typeof marker.setMap === 'function') {
							marker.setMap(null)
						}
					} catch (_) {}
					// Prefer AdvancedMarkerElement to avoid deprecation notice; fall back safely
					try {
						if (AdvancedMarkerElement) {
							marker = new AdvancedMarkerElement({ map, position: pos })
						} else {
							marker = new google.maps.Marker({ map, position: pos })
						}
					} catch (err) {
						console.warn('Advanced marker failed, falling back to classic Marker.', err)
						marker = new google.maps.Marker({ map, position: pos })
					}
				}

				async function performGeocode (query) {
					const value = (query ?? '').trim()
					if (!value) { return }
					if (!geocoder) { geocoder = new google.maps.Geocoder() }
					try {
						const { results } = await geocoder.geocode({
							address: value,
							componentRestrictions: { country: 'US' }
						})
						if (!Array.isArray(results) || results.length === 0) { return }
						const loc = results[0].geometry?.location
						if (!loc) { return }
						const pos = { lat: loc.lat(), lng: loc.lng() }
						setCenterAndMarker(pos)
						// Keep Livewire state in sync so backend-rendered bits remain accurate
						if (typeof window.$wire !== 'undefined') {
							try {
								$wire.set('search', value)
								$wire.set('lat', pos.lat)
								$wire.set('lng', pos.lng)
							} catch (_) {}
							try { $wire.dispatch('map-center-updated', { lat: pos.lat, lng: pos.lng }) } catch (_) {}
						}
					} catch (e) {
						console.error('Client geocoding failed:', e)
					}
				}

				const init = async () => {
					try {
						const { Map } = await google.maps.importLibrary('maps')
						try {
							({ AdvancedMarkerElement } = await google.maps.importLibrary('marker'))
						} catch (err) {
							console.warn('AdvancedMarkerElement not available; will fall back to standard Marker.', err)
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
						setCenterAndMarker(center)

						// Intercept Livewire events and DOM events to do client-side geocoding in production
						const input = document.getElementById('text-input')
						const button = document.getElementById('text-input-button')
						if (input) {
							// Capture phase to beat Livewire's keydown handler
							input.addEventListener('keydown', (e) => {
								if (e.key === 'Enter') {
									e.preventDefault()
									e.stopImmediatePropagation()
									performGeocode(input.value)
								}
							}, true)
						}
						if (button) {
							button.addEventListener('click', (e) => {
								e.preventDefault()
								e.stopImmediatePropagation()
								performGeocode(input ? input.value : '')
							})
						}

						window.addEventListener('map-center-updated', (event) => {
							const { lat, lng } = event.detail || {}
							if (lat == null || lng == null) { return }
							const pos = { lat: parseFloat(lat), lng: parseFloat(lng) }
							setCenterAndMarker(pos)
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