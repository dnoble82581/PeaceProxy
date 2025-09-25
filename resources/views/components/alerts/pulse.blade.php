@props(['textClasses' => '', 'primaryColor' => 'bg-rose-500', 'secondaryColor' => 'bg-rose-400'])
<div {{ $attributes->merge(['class'=>'flex items-center gap-2']) }}>
	<div class="">
		<span class="relative flex size-3">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full {{ $secondaryColor }} opacity-75"></span>
			<span class="relative inline-flex size-3 rounded-full {{ $primaryColor }}"></span>
		</span>
	</div>
	<div class="{{ $textClasses }}">
		{{ $slot }}
	</div>

</div>