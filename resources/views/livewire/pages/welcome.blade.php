<?php

	use Livewire\Attributes\Layout;
	use Livewire\Attributes\Title;
	use Livewire\Volt\Component;

	new #[Layout ('layouts.guest')] #[Title('Welcome')] class extends Component {}

?>

<div>
	<section id="hero">
		<div class="flex flex-col md:flex-row px-4 md:px-8 pt-4 items-center justify-between">
			<x-logos.app-logo-icon />

			<!-- Mobile menu button -->
			<button
					id="mobile-menu-button"
					class="md:hidden mt-4 p-2 focus:outline-none">
				<svg
						class="w-6 h-6"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						xmlns="http://www.w3.org/2000/svg">
					<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M4 6h16M4 12h16M4 18h16"></path>
				</svg>
			</button>

			<!-- Mobile menu (hidden by default) -->
			<div
					id="mobile-menu"
					class="hidden w-full md:hidden mt-2 py-2 space-y-2">
				<a
						class="block py-2 px-4 text-center hover:text-gray-500 uppercase"
						href="#">Products</a>
				<a
						class="block py-2 px-4 text-center hover:text-gray-500 uppercase"
						href="#">About</a>
				<a
						class="block py-2 px-4 text-center hover:text-gray-500 uppercase"
						href="#">Gallery</a>
				@auth
					<a
							class="block py-2 px-4 text-center hover:text-gray-500 uppercase"
							href="{{ route('dashboard', ['tenantSubdomain' => tenant()->subdomain]) }}">Dashboard</a>
				@else
					<a
							class="block py-2 px-4 text-center hover:text-gray-500 uppercase"
							href="{{ route('login') }}">Login</a>
					<a
							class="block py-2 px-4 text-center hover:text-gray-500 uppercase"
							href="{{ route('register') }}">Register</a>
				@endauth
			</div>

			<!-- Desktop menu -->
			<div class="hidden md:flex uppercase space-x-4">
				<a
						class="hover:text-gray-500"
						href="#">Products</a>
				<a
						class="hover:text-gray-500"
						href="#">About</a>
				<a
						class="hover:text-gray-500"
						href="#">Gallery</a>
			</div>
			@auth
				<div class="hidden md:flex uppercase space-x-4">
					<a
							class="hover:text-gray-500"
							href="{{ route('dashboard', ['tenantSubdomain' => tenant()->subdomain]) }}">Dashboard</a>
				</div>
			@else
				<div class="hidden md:flex uppercase space-x-4">
					<a
							class="hover:text-gray-500"
							href="{{ route('login') }}">Login</a>
					<a
							class="hover:text-gray-500"
							href="{{ route('register') }}">Register</a>
				</div>
			@endauth
		</div>

		<div class="min-h-[80vh] md:h-screen flex items-center justify-center px-4 py-8 md:py-0">
			<div class="text-center space-y-4 md:space-y-8">
				<div class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight uppercase">
					<span>Innovative</span>
					<span class="text-blue-500">Solutions</span>
				</div>
				<div class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl uppercase text-zinc-900 tracking-wider dark:text-zinc-100">
					<span class="block">For High-Stakes</span>
					<span class="block mt-2 text-blue-500">Conversations</span>
				</div>
				<div class="mt-4 md:mt-8 text-base md:text-lg">
					<p>Access your organization's portal or create a new one</p>
				</div>
				<div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4 mt-4">
					<a
							href="{{ route('login') }}"
							class="w-full sm:w-auto">
						<x-button class="w-full sm:w-auto">Login to Your Portal</x-button>
					</a>
					<a
							href="{{ route('register') }}"
							class="w-full sm:w-auto">
						<x-button
								color="secondary"
								class="w-full sm:w-auto">Register New Organization
						</x-button>
					</a>
				</div>
			</div>
		</div>
	</section>

	<section
			id="real-time-communication"
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-800 flex items-center justify-between">
		<div class="flex flex-col lg:flex-row items-center w-full px-4 sm:px-8 md:px-12 lg:px-20 gap-8">
			<div class="w-full lg:w-1/2 order-2 lg:order-1">
				<div class="space-y-4 md:space-y-8">
					<h2 class="text-3xl md:text-4xl font-bold">
						<span class="text-zinc-100">Real-Time</span>
						<span class="text-primary-500">Communication</span>
					</h2>
					<p class="dark:text-zinc-100 text-zinc-100">Stay connected when it counts. Our platform powers
					                                            instant, secure communication across teams—no delays, no
					                                            confusion.</p>
				</div>

				<ul class="mt-4 p-4 space-y-4 md:space-y-8 text-white">
					<li class="list-disc">
						<span class="font-semibold">Command-Level Visibility:</span>
						<span>Monitor active negotiations and field communications in real time, enabling informed decision-making and strategic oversight.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Secure Role-Based Communication:</span>
						<span>Maintain control with team-specific channels and permissions tailored to operational hierarchies.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Integrated Audio & Messaging:</span>
						<span>Facilitate immediate, multi-channel communication to reduce response time and improve situational awareness.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Reliable in the Field:</span>
						<span>Built for mobility and resilience, ensuring your teams stay connected no matter the environment.</span>
					</li>
				</ul>
			</div>
			<div class="w-full lg:w-1/2 flex justify-center lg:justify-end order-1 lg:order-2">
				<div class="h-[300px] sm:h-[400px] md:h-[500px] lg:h-[600px] w-full max-w-[500px] lg:max-w-[600px] bg-slate-700 rounded-lg flex items-center justify-center">
					<span class="text-xl sm:text-2xl md:text-3xl text-zinc-100">Image Placeholder</span>
				</div>
			</div>
		</div>
	</section>

	<section
			id="mood-tracking"
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-100 flex items-center">
		<div class="flex flex-col lg:flex-row items-center w-full px-4 sm:px-8 md:px-12 lg:px-20 gap-8">
			<div class="w-full lg:w-1/2 order-2 lg:order-2">
				<div class="space-y-4 md:space-y-8">
					<h2 class="text-3xl md:text-4xl font-bold">
						<span class="text-zinc-900">Mood</span>
						<span class="text-primary-500">Tracking</span>
					</h2>
					<p class="">Our integrated mood tracking feature uses advanced voice analysis and behavioral cues to
					            generate a real-time line chart that reflects the subject’s emotional state over the
					            course of the interaction. This visual tool supports negotiators and command staff by
					            providing critical context during dynamic conversations.</p>
				</div>

				<ul class="mt-4 p-4 space-y-4 md:space-y-8">
					<li class="list-disc">
						<span class="font-semibold">Monitor De-escalation Progress:</span>
						<span>See fluctuations in stress, agitation, or cooperation levels as negotiations unfold.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Identify Tactical Opportunities:</span>
						<span>Use emotional patterns to determine optimal moments for intervention, redirection, or escalation.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Improve Team Coordination:</span>
						<span>Share live mood trends with field teams, behavioral health experts, and command units to align strategy in real time.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Built for Operational Tempo:</span>
						<span>The mood chart updates automatically and requires no additional operator input, ensuring zero distractions during live incidents.</span>
					</li>
				</ul>
			</div>
			<div class="w-full lg:w-1/2 flex justify-center lg:justify-start order-1 lg:order-1">
				<div class="h-[300px] sm:h-[400px] md:h-[500px] lg:h-[600px] w-full max-w-[500px] lg:max-w-[800px] bg-slate-700 rounded-lg flex items-center justify-center">
					<span class="text-xl sm:text-2xl md:text-3xl text-zinc-100">Image Placeholder</span>
				</div>
			</div>
		</div>
	</section>

	<script>
		document.addEventListener('DOMContentLoaded', function () {
			// Mobile menu toggle
			const mobileMenuButton = document.getElementById('mobile-menu-button')
			const mobileMenu = document.getElementById('mobile-menu')

			if (mobileMenuButton && mobileMenu) {
				mobileMenuButton.addEventListener('click', function () {
					mobileMenu.classList.toggle('hidden')
				})
			}
		})
	</script>
</div>
