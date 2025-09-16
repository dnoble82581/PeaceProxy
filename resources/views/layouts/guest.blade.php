<!DOCTYPE html>
<html
		lang="en"
		x-data="tallstackui_darkTheme({ default: 'dark' })">
<head>
	@include('layouts.partials._head')
</head>
<body
		class="antialiased text-dark-800 dark:text-white bg-white dark:bg-dark-900"
		x-bind:class="{ 'dark bg-dark-900': darkTheme, 'bg-white': !darkTheme }">

{{-- Navigation --}}
{{--@include('partials.nav')--}}

{{-- Main Content --}}
<main class="min-h-screen">
	{{ $slot }}
</main>

{{-- Footer --}}
{{--@include('partials.footer')--}}
@include('layouts.partials._tail')
</body>
</html>
