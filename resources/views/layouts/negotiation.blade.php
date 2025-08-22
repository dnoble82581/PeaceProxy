<!DOCTYPE html>
<html
		lang="en"
		x-data="tallstackui_darkTheme()">
<head>
	<meta charset="UTF-8" />
	<meta
			name="viewport"
			content="width=device-width, initial-scale=1.0" />

	<meta
			name="csrf-token"
			content="{{ csrf_token() }}">


	<title>{{ $title ?? 'Dashboard - Peace Proxy' }}</title>
	<meta
			name="description"
			content="{{ $description ?? 'Law enforcement negotiation dashboard.' }}">
	<link
			rel="icon"
			href="{{ asset('favicon.ico') }}" />

	<link
			rel="stylesheet"
			href="https://use.typekit.net/ccn6txi.css">

	<tallstackui:script />

	{{-- Styles --}}
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	@livewireStyles
	@stack('head')
</head>
<body
		class="bg-gray-100 text-gray-900 dark:bg-dark-900 dark:text-white antialiased min-h-screen"
		x-bind:class="{ 'dark bg-dark-900': darkTheme, 'bg-white': !darkTheme }">

<livewire:navigation.negotiation-nav :negotiation="$negotiation ?? null" />

{{ $slot }}

{{-- Scripts --}}
@livewireScripts
@tallStackUiScript
@stack('scripts')
</body>
</html>
