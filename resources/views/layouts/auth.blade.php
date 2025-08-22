<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta
			name="viewport"
			content="width=device-width, initial-scale=1.0" />

	<title>{{ $title ?? 'Login - Peace Proxy' }}</title>
	<meta
			name="description"
			content="{{ $description ?? 'Secure login for Peace Proxy users.' }}">
	<link
			rel="icon"
			href="{{ asset('favicon.ico') }}" />

	<link
			rel="stylesheet"
			href="https://use.typekit.net/ccn6txi.css">

	{{-- Styles --}}
	@vite('resources/css/app.css')
	@livewireStyles
	@stack('head')
</head>
<body class="bg-gray-100 antialiased flex items-center justify-center min-h-screen p-4">

{{-- Auth Card --}}
<div class="w-full {{ Route::currentRouteName()==='login' ? 'max-w-lg' : 'max-w-4xl' }} bg-white shadow-xl rounded-2xl p-8">
	<a href="/">
		<x-logos.app-logo-icon
				main="text-2xl"
				secondary="text-md" />
	</a>
	@if (Route::currentRouteName() === 'login')
		<h3 class="text-center p-2">Sign Into Your Domain</h3>
	@else
		<h3 class="text-center p-2">Register A New Agency</h3>
	@endif
	{{ $slot }}

	@if (Route::currentRouteName() === 'login')
		<p class="text-sm">Need to create an account? <a
					class="text-primary-500 hover:text-primary-600"
					href="{{ route('register') }}"
			>Register here.</a></p>
	@else
		<p class="text-sm">Already have an account? <a
					class="text-primary-500 hover:text-primary-600"
					href="{{ route('login') }}"
			>Login here</a></p>
	@endif
</div>

{{-- Scripts --}}
@livewireScripts
@vite('resources/js/app.js')
@stack('scripts')
</body>
</html>
