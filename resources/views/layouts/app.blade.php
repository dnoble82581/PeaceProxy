<!DOCTYPE html>
<html
		lang="en"
		x-data="tallstackui_darkTheme()">
<head>
	@include('layouts.partials._head')
</head>
<body
		class="bg-gray-100 text-dark-800 dark:bg-dark-900 dark:text-white antialiased min-h-screen"
		x-bind:class="{ 'dark bg-dark-900': darkTheme, 'bg-white': !darkTheme }">

<x-layout class="overflow-y-visible">
	<x-slot:header>
		<x-layout.header>
			<x-slot:left>
				<p class="dark:text-white">
					{{ authUser()->tenant->agency_name }}
				</p>
			</x-slot:left>
			<x-slot:right>
				<div class="flex items-center gap-4">
					<x-theme-switch only-icons />
					<x-dropdown text="{{ authUser()->name }}">
						<x-slot:header>
							<p class="text-xs text-dark-300 ml-3">{{ authUser()->permissions ?? 'User' }}</p>
						</x-slot:header>
						<form
								method="POST"
								action="{{ route('logout') }}">
							@csrf
							<x-dropdown.items
									text="Logout"
									onclick="event.preventDefault(); this.closest('form').submit();" />
						</form>
					</x-dropdown>
				</div>
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
					:route="route('dashboard.users', authUser()->tenant->subdomain)"
					:current="request()->routeIs('dashboard.users')"
					text="Users"
					icon="users"
			/>
			<x-side-bar.item
					wire:navigate
					:route="route('pages.dashboard.assessments', authUser()->tenant->subdomain)"
					:current="request()->routeIs('pages.dashboard.assessments')"
					text="Assessments"
					icon="chart-bar"
			/>
			<x-side-bar.item
					wire:navigate
					:route="route('dashboard.settings', authUser()->tenant->subdomain)"
					:current="request()->routeIs('dashboard.settings')"
					text="Settings"
					icon="cog"
			/>

			<div class="absolute bottom-8">
				<x-side-bar.item
						target="_blank"
						href="https://docs.peaceproxy.com"
						text="Documentation"
						icon="arrow-top-right-on-square"
				/>
				<x-side-bar.item
						target="_blank"
						href="https://github.com/dnoble82581/PeaceProxy"
						text="Git Hub Repository"
						icon="arrow-top-right-on-square"
				/>
			</div>
		</x-side-bar>
	</x-slot:menu>
	<x-slot:footer>
		<h1 class="text-white">test</h1>
	</x-slot:footer>

	{{ $slot }}
</x-layout>

{{-- Scripts --}}
@include('layouts.partials._tail')
</body>
</html>
