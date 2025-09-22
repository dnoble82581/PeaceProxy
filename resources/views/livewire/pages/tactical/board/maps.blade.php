<?php

	new class extends \Livewire\Volt\Component {}

?>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">


	<div
			x-data="mapWidget({
    lat: {{ $lat ?? 41.6611 }},
    lng: {{ $lng ?? -91.5302 }},
    mapId: '{{ config('services.maps.map_id') }}' // optional
  })"
			x-init="init()"
			class="h-96 w-full rounded-2xl border"
	>

		<div
				x-ref="canvas"
				class="h-full w-full"></div>

	</div>

	@php
		/** @var \App\Services\MapsStaticService $static */
		$static = app(\App\Services\MapsStaticService::class);
		$url = $static->incidentImageUrl([
			'lat' => 41.6611,
			'lng' => -91.5302,
			'width' => 900,
			'height' => 540,
			// 'encodedPolyline' => $route->encoded_polyline ?? null,
		]);

	@endphp

</div>

<script>
	document.addEventListener('alpine:init', () => {
		Alpine.data('mapWidget', (opts) => ({
			map: null,
			marker: null,
			async init () {
				const { Map } = await google.maps.importLibrary('maps')
				const { AdvancedMarkerElement } = await google.maps.importLibrary('marker')

				const center = { lat: Number(opts.lat), lng: Number(opts.lng) }

				this.map = new Map(this.$refs.canvas, {
					center,
					zoom: 13,
					mapId: opts.mapId || 'DEMO_MAP_ID', // use your own if set
					gestureHandling: 'greedy',
				})

				this.marker = new AdvancedMarkerElement({ map: this.map, position: center })

				// Example: recenter from Livewire
				window.addEventListener('map:recenter', (e) => {
					const pos = e.detail
					this.map.setCenter(pos)
					this.marker.position = pos
				})
			}
		}))
	})
</script>

