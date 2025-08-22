@props(['confidenceScore'])

@php
	if ($confidenceScore >= 0.7) {
		$color = 'teal';
		$icon = 'presentation-chart-line';
	} elseif ($confidenceScore >= 0.3) {
		$color = 'blue';
		$icon = 'tag';
	} else {
		$color = 'red';
		$icon = 'tag';
	}
@endphp

<x-badge
		:color="$color"
		xs
		round
		:icon="$icon">
	{{ $confidenceScore }}
</x-badge>