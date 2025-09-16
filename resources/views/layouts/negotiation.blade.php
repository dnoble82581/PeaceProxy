<!DOCTYPE html>
<html
		lang="en"
		x-data="tallstackui_darkTheme({ default: 'dark' })">
<head>
	@include('layouts.partials._head')
</head>
<body
		class="bg-gray-100 text-dark-800 dark:bg-dark-900 dark:text-white antialiased min-h-screen"
		x-bind:class="{ 'dark bg-dark-900': darkTheme, 'bg-white': !darkTheme }">

<livewire:navigation.negotiation-nav :negotiation="$negotiation ?? null" />

{{ $slot }}

{{-- Scripts --}}
@include('layouts.partials._tail')
</body>
</html>
