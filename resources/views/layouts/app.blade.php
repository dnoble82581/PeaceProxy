<!DOCTYPE html>
<html
		lang="en"
		x-data="tallstackui_darkTheme()">
<head>
	<meta charset="UTF-8" />
	<meta
			name="viewport"
			content="width=device-width, initial-scale=1.0" />

	<title>{{ $title ?? 'Dashboard - Peace Proxy' }}</title>
	<meta
			name="description"
			content="{{ $description ?? 'Law enforcement negotiation dashboard.' }}">

	<meta
			name="csrf-token"
			content="{{ csrf_token() }}">

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
<body
		class="bg-gray-100 text-gray-900 dark:bg-dark-900 antialiased min-h-screen"
		x-bind:class="{ 'dark bg-dark-900': darkTheme, 'bg-white': !darkTheme }">

<x-layout>
	<x-slot:header>
		<x-layout.header>
			<x-slot:left>
				<p class="dark:text-white">
					{{ authUser()->tenant->agency_name }}
				</p>
			</x-slot:left>
			<x-slot:right>
				<x-theme-switch only-icons />
				<x-dropdown text="Hello, {{ authUser()->name }}!">
					<form
							method="POST"
							action="{{ route('logout') }}">
						@csrf
						<x-dropdown.items
								text="Logout"
								onclick="event.preventDefault(); this.closest('form').submit();" />
					</form>
				</x-dropdown>
			</x-slot:right>
		</x-layout.header>
	</x-slot:header>

	<x-slot:menu>
		<x-side-bar>
			<x-slot:brand>
				<div class="mt-4">
					<x-logos.app-logo-icon />
				</div>
			</x-slot:brand>
			<x-side-bar.item
					wire:navigate
					:route="route('dashboard', authUser()->tenant->subdomain)"
					:current="request()->routeIs('dashboard')"
					text="Home"
					icon="home"
			/>
			<x-side-bar.item
					wire:navigate
					:route="route('dashboard.negotiations', authUser()->tenant->subdomain)"
					:current="request()->routeIs('dashboard.negotiations')"
					text="Negotiations"
					icon="archive-box"
			/>
			<x-side-bar.item
					wire:navigate
					:route="route('dashboard.settings', authUser()->tenant->subdomain)"
					:current="request()->routeIs('dashboard.settings')"
					text="Settings"
					icon="cog"
			/>

		</x-side-bar>
	</x-slot:menu>

	{{ $slot }}
</x-layout>

{{-- Scripts --}}
@livewireScripts
@vite('resources/js/app.js')
@stack('scripts')
</body>
</html>
