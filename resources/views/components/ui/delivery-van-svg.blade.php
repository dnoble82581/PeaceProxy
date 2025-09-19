@props([
    'color' => 'currentColor',
    'width' => '20',
    'height' => '20',
    'class' => '',
])

<svg
		xmlns="http://www.w3.org/2000/svg"
		viewBox="0 0 24 24"
		{{ $attributes->merge(['class' => $class]) }}
		width="{{ $width }}"
		height="{{ $height }}"
		fill="none"
		stroke="{{ $color }}"
		stroke-width="1"
		stroke-linecap="round"
		stroke-linejoin="round"
>
	<!-- Van body -->
	<path d="M3 12h13v6H3z" />

	<!-- Van cabin -->
	<path d="M16 12V7c0-1.1-.9-2-2-2H3v7" />

	<!-- Front wheel -->
	<circle
			cx="6"
			cy="18"
			r="2" />

	<!-- Rear wheel -->
	<circle
			cx="14"
			cy="18"
			r="2" />

	<!-- Front window -->
	<path d="M8 7v3" />

	<!-- Headlight -->
	<path d="M3 9h2" />

	<!-- Cargo area -->
	<path d="M16 12h3l2 3v3h-5" />
</svg>