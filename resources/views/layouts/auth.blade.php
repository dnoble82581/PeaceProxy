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

	<link
			rel="icon"
			href="{{ asset('assets/favicon.png') }}"
			type="image/png">

	{{-- Styles --}}
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	@livewireStyles
	@stack('head')
</head>
<body
		class="bg-gray-100 text-dark-800 dark:bg-dark-900 dark:text-white antialiased flex items-center justify-center min-h-screen p-4"
		x-bind:class="{ 'dark bg-dark-900': darkTheme, 'bg-white': !darkTheme }">

{{-- Auth Card --}}
<div class="w-full {{ Route::currentRouteName()==='login' ? 'max-w-lg' : 'max-w-4xl' }} bg-white dark:bg-dark-800 shadow-xl rounded-2xl p-8">
	<a href="/">
		<x-logos.app-logo-icon
				main="text-2xl"
				secondary="text-md" />
	</a>
	@if (Route::currentRouteName() === 'login')
		<h3 class="text-center p-2 text-dark-800 dark:text-white">Sign Into Your Domain</h3>
	@else
		<h3 class="text-center p-2 text-dark-800 dark:text-white">Register A New Agency</h3>
	@endif
	{{ $slot }}

	@if (Route::currentRouteName() === 'login')
		<p class="text-sm text-dark-800 dark:text-white">Need to create an account? <a
					class="text-primary-500 hover:text-primary-600"
					href="{{ route('register') }}"
			>Register here.</a></p>
	@else
		<p class="text-sm text-dark-800 dark:text-white">Already have an account? <a
					class="text-primary-500 hover:text-primary-600"
					href="{{ route('login') }}"
			>Login here</a></p>
	@endif
</div>

{{-- Scripts --}}
@include('layouts.partials._tail')
</body>
</html>
