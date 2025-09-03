<!DOCTYPE html>
<html
		lang="en"
		x-data="tallstackui_darkTheme()">
<head>
	<meta charset="UTF-8">
	<meta
			name="viewport"
			content="width=device-width, initial-scale=1.0">

	<title>{{ $title ?? 'Peace Proxy | Crisis negotiation Platform' }}</title>
	<meta
			name="description"
			content="{{ $description ?? 'Peace Proxy is a real-time collaboration tool for law enforcement negotiators and tactical teams.' }}">
	<meta
			name="robots"
			content="index, follow">

	{{-- Open Graph --}}
	<meta
			property="og:title"
			content="{{ $title ?? 'Peace Proxy' }}">
	<meta
			property="og:description"
			content="{{ $description ?? 'Modern law enforcement negotiation tool.' }}">
	<meta
			property="og:url"
			content="{{ url()->current() }}">
	<meta
			property="og:image"
			content="{{ asset('images/social-card.jpg') }}">
	<meta
			property="og:type"
			content="website">

	{{-- Twitter Card --}}
	<meta
			name="twitter:card"
			content="summary_large_image">
	<meta
			name="twitter:title"
			content="{{ $title ?? 'Peace Proxy' }}">
	<meta
			name="twitter:description"
			content="{{ $description ?? 'Powerful SaaS for crisis teams.' }}">
	<meta
			name="twitter:image"
			content="{{ asset('images/social-card.jpg') }}">

	{{-- Canonical link --}}
	<link
			rel="canonical"
			href="{{ url()->current() }}">

	{{-- Favicon --}}
	<link
			rel="icon"
			href="{{ asset('assets/favicon.png') }}"
			type="image/png">

	<link
			rel="stylesheet"
			href="https://use.typekit.net/ccn6txi.css">
	@vite('resources/css/app.css')
	@livewireStyles
	@stack('head')
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
