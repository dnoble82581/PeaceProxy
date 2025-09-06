<?php

	use Livewire\Attributes\Layout;
	use Livewire\Attributes\Title;
	use Livewire\Volt\Component;

	new #[Layout('layouts.guest')] #[Title('About Us | Peace Proxy')] class extends Component {}

?>

<div>
	<!-- Navigation -->
	<div class="flex flex-col md:flex-row px-4 md:px-8 pt-4 items-center justify-between pb-4">
		<a href="/">
			<x-logos.app-logo-icon />
		</a>

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
					class="block py-2 px-4 text-center hover:text-gray-500"
					href="https://docs.peaceproxy.com">Documentation</a>
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
		<div class="hidden md:flex space-x-4">
			<x-link
					color="secondary"
					class="hover:text-gray-400 font-semibold"
					icon="arrow-top-right-on-square"
					href="https://github.com/dnoble82581/PeaceProxy">Git Hub
			</x-link>
			<x-link
					color="secondary"
					class="hover:text-gray-400 font-semibold"
					icon="arrow-top-right-on-square"
					href="https://docs.peaceproxy.com">Documentation
			</x-link>
			<x-link
					color="secondary"
					class="hover:text-gray-400 font-semibold"
					icon="arrow-top-right-on-square"
					href="{{ route('about') }}">About Us
			</x-link>
		</div>
		@auth
			<div class="hidden md:flex uppercase space-x-4">
				<x-link
						color="secondary"
						class="hover:text-gray-400"
						href="{{ route('dashboard', ['tenantSubdomain' => tenant()->subdomain]) }}">Dashboard
				</x-link>
			</div>
		@else
			<div class="hidden md:flex uppercase space-x-4">
				<x-link
						color="secondary"
						class="hover:text-gray-400"
						href="{{ route('login') }}">Login
				</x-link>
				<x-link
						color="secondary"
						class="hover:text-gray-400"
						href="{{ route('register') }}">Register
				</x-link>
			</div>
		@endauth
	</div>

	<!-- About the Creator Section -->
	<section class="py-12 md:py-16 lg:py-20 bg-zinc-100">
		<div class="container mx-auto px-4 sm:px-8 md:px-12 lg:px-20">
			<!-- Header with image -->
			<div class="flex flex-col items-center text-center mb-12">
				<h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-8">
					<span class="text-zinc-900">Meet the</span>
					<span class="text-primary-500">Creator</span>
				</h1>

				<!-- Featured image with glow effect -->
				<div class="relative max-w-md mx-auto mb-8">
					<div class="absolute -inset-1 bg-primary-500 opacity-20 blur-lg rounded-lg"></div>
					<img
							src="https://peace-proxy-pro.s3.us-east-1.amazonaws.com/public/assets/AboutImage.webp"
							alt="Peace Proxy Creator"
							class="relative z-10 rounded-lg shadow-xl max-w-full h-auto object-cover"
							style="max-height: 500px;">
				</div>
			</div>

			<!-- Content in cards layout -->
			<div class="max-w-4xl mx-auto">
				<!-- Introduction Card -->
				<div class="bg-white rounded-lg shadow-md p-6 mb-8">
					<h2 class="text-2xl font-semibold text-primary-600 mb-4">The Journey</h2>
					<div class="prose prose-lg max-w-none">
						<p>
							Hi, I'm the creator of Peace Proxy — a tool built from the front lines of crisis and
							compassion.
						</p>

						<p>
							I'm a Police Officer, Crisis Negotiator, Drone Pilot, Defensive Tactics Instructor, and
							CPR Instructor. But before I put on the badge, I was a Social Worker helping homeless
							men and families find stability. And before that, I was a Youth Pastor, walking with
							young people through some of the toughest seasons of their lives.
						</p>

						<p>
							Every step of my journey has been rooted in one purpose: helping people in crisis find a
							way forward.
						</p>
					</div>
				</div>

				<!-- Why Peace Proxy Card -->
				<div class="bg-white rounded-lg shadow-md p-6 mb-8">
					<h2 class="text-2xl font-semibold text-primary-600 mb-4">Why Peace Proxy</h2>
					<div class="prose prose-lg max-w-none">
						<p>
							That's why I built Peace Proxy.
						</p>
						<p>
							I know what it's like to be on the other end of a phone call that could change—or end—a
							life. I know the pressure of needing the right words in the worst moments. Peace Proxy
							was born out of that tension. It's designed to support Hostage Negotiators, Crisis
							Teams, and Mental Health Responders with tools and strategies that lead to peaceful
							outcomes.
						</p>

						<p>
							This isn't just tech. This is mission-driven work.
						</p>
					</div>
				</div>

				<!-- Personal Philosophy Card -->
				<div class="bg-white rounded-lg shadow-md p-6 mb-8">
					<h2 class="text-2xl font-semibold text-primary-600 mb-4">Personal Philosophy</h2>
					<div class="prose prose-lg max-w-none">
						<p>
							My faith is the foundation of everything I do. It fuels my desire to serve, to listen,
							to lead with humility, and to stand in the gap when others are hurting. I also happen to
							be a woodworker, musician, and freelance web developer—all outlets that help me stay
							grounded and creative. In college, I volunteered as a Big Brother for three years, and
							that experience shaped how I see mentorship and second chances.
						</p>
						<p>
							Peace Proxy is more than software. It's a reflection of everything I believe: that every
							life has value, that calm is contagious, and that with the right support, peace is
							possible—even in chaos.
						</p>
					</div>
				</div>

				<!-- Closing Card -->
				<div class="bg-white rounded-lg shadow-md p-6 mb-8 text-center">
					<div class="prose prose-lg max-w-none">
						<p class="text-xl font-medium text-primary-700">
							Thanks for being here.
						</p>
						<p class="text-xl font-medium text-primary-700">
							Let's do good work.
						</p>
					</div>
				</div>

				<!-- Mission Statement Card -->
				<div class="bg-primary-50 border-l-4 border-primary-500 rounded-lg shadow-md p-6">
					<h2 class="text-2xl font-bold mb-4">
						<span class="text-zinc-900">Our</span>
						<span class="text-primary-500">Mission</span>
					</h2>

					<p class="text-lg">
						At Peace Proxy, our mission is to empower crisis negotiation teams with innovative tools
						that enhance communication, improve situational awareness, and increase the likelihood
						of peaceful resolutions in high-stakes scenarios.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="bg-zinc-100 pt-12 pb-6">
		<div class="container mx-auto px-4 sm:px-8 md:px-12 lg:px-20">
			<!-- Footer top section with logo, links and info -->
			<div class="flex flex-col md:flex-row justify-between items-center md:items-start mb-8 gap-8">
				<!-- Logo and tagline -->
				<div class="flex flex-col items-center md:items-start space-y-4 md:w-1/3">
					<a href="/">
						<x-logos.app-logo-icon />
					</a>
					<p class="text-zinc-600 text-center md:text-left">Innovative solutions for high-stakes
					                                                  conversations.</p>
				</div>

				<!-- Quick links -->
				<div class="flex flex-col items-center md:items-start space-y-4 md:w-1/3">
					<h3 class="text-lg font-bold text-zinc-900">Resources</h3>
					<ul class="space-y-2 text-center md:text-left">
						<li>
							<x-link
									icon="arrow-top-right-on-square"
									href="https://docs.peaceproxy.com"
							>Documentation
							</x-link>
						</li>
					</ul>
				</div>

				<!-- Contact info -->
				<div class="flex flex-col items-center md:items-start space-y-4 md:w-1/3">
					<h3 class="text-lg font-bold text-zinc-900">Contact Us</h3>
					<ul class="space-y-2 text-center md:text-left">
						<li><a
									href="/contact"
									class="text-primary-600 hover:text-primary-800 transition-colors">Contact Form</a>
						</li>
						<li><a
									href="mailto:support@peaceproxy.com"
									class="text-primary-600 hover:text-primary-800 transition-colors">support@peaceproxy.com</a>
						</li>
					</ul>
				</div>
			</div>

			<!-- Divider -->
			<div class="border-t border-zinc-300 my-6"></div>

			<!-- Copyright and legal -->
			<div class="flex flex-col md:flex-row justify-between items-center text-sm text-zinc-500">
				<p>© 2025 PeaceProxy. All rights reserved.</p>
				<div class="flex space-x-4 mt-4 md:mt-0">
					<a
							href="/privacy"
							class="hover:text-zinc-700 transition-colors">Privacy Policy</a>
					<a
							href="/terms"
							class="hover:text-zinc-700 transition-colors">Terms of Service</a>
				</div>
			</div>
		</div>
	</footer>

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