@props(['confidenceScore'])

@php
	use App\Enums\General\ConfidenceScore;
	
	if ($confidenceScore == ConfidenceScore::High || $confidenceScore == ConfidenceScore::VeryHigh) {
		$color = 'teal';
		$icon = 'presentation-chart-line';
	} elseif ($confidenceScore == ConfidenceScore::Medium) {
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
	{{ $confidenceScore->label() }}
</x-badge>