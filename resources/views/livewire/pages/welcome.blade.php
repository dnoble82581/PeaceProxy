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
				<div class="mt-4 md:mt-8 text-base md:text-lg max-w-3xl mx-auto">
					<p>A comprehensive platform for crisis management and negotiation, providing advanced tools to
					   navigate and resolve high-stakes situations effectively.</p>
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
					<img
							src="{{ Vite::asset('resources/images/chat-light.png') }}"
							alt="">
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
				<div class="h-[300px] sm:h-[400px] md:h-[500px] lg:h-[600px] w-full max-w-[500px] lg:max-w-[800px] rounded-lg flex items-center justify-center">
					<img
							src="{{ Vite::asset('resources/images/mood-tracking.png') }}"
							alt="">
				</div>
			</div>
		</div>
	</section>

	<section
			id="unified-interface"
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-800 relative overflow-hidden">
		<!-- Large background image container -->
		<div class="absolute inset-0 z-0 opacity-90">
			<img
					src="{{ Vite::asset('resources/images/unified-interface.png') }}"
					alt="Unified Interface"
					class="w-full h-full object-cover object-center"
			/>
			<div class="absolute inset-0 bg-zinc-900 opacity-40"></div>
		</div>

		<!-- Content overlay -->
		<div class="relative z-10 flex flex-col w-full h-full px-4 sm:px-8 md:px-12 lg:px-20 py-8 md:py-12">
			<!-- Main image showcase - larger and more prominent -->
			<div class="w-full flex justify-center mb-10 md:mb-14 mt-4 md:mt-8">
				<div class="w-full max-w-6xl rounded-lg overflow-hidden shadow-2xl border-3 border-primary-500 transform transition-all duration-500 hover:scale-[1.02] relative">
					<!-- Subtle glow effect -->
					<div class="absolute -inset-1 bg-primary-500 opacity-20 blur-lg rounded-lg"></div>
					<img
							src="{{ Vite::asset('resources/images/unified-interface-light.png') }}"
							alt="Unified Interface"
							class="w-full h-auto relative z-10"
					/>
				</div>
			</div>

			<!-- Text content in a more subtle semi-transparent card -->
			<div class="w-full max-w-5xl mx-auto bg-zinc-800 bg-opacity-70 rounded-lg p-6 md:p-8 backdrop-blur-sm border border-zinc-700">
				<div class="space-y-4 md:space-y-6">
					<h2 class="text-3xl md:text-4xl font-bold text-center mb-4">
						<span class="text-zinc-100">Unified</span>
						<span class="text-primary-500">Interface</span>
					</h2>
					<p class="text-zinc-100 text-center max-w-3xl mx-auto">Experience seamless operations with our
					                                                       intuitive,
					                                                       all-in-one command interface. Designed for
					                                                       clarity and
					                                                       efficiency during critical situations.</p>
				</div>

				<div class="mt-6 md:mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 text-white">
					<div class="bg-zinc-800 bg-opacity-70 p-4 rounded-lg">
						<h3 class="font-semibold text-primary-400 mb-2">Centralized Command Dashboard</h3>
						<p>Access all critical tools and information from a single, unified view—eliminating the need to
						   switch between applications.</p>
					</div>
					<div class="bg-zinc-800 bg-opacity-70 p-4 rounded-lg">
						<h3 class="font-semibold text-primary-400 mb-2">Customizable Workspace</h3>
						<p>Tailor the interface to your team's specific operational requirements and preferences.</p>
					</div>
					<div class="bg-zinc-800 bg-opacity-70 p-4 rounded-lg">
						<h3 class="font-semibold text-primary-400 mb-2">Intuitive Navigation</h3>
						<p>Minimize training time with our user-friendly design that prioritizes accessibility during
						   high-stress situations.</p>
					</div>
					<div class="bg-zinc-800 bg-opacity-70 p-4 rounded-lg">
						<h3 class="font-semibold text-primary-400 mb-2">Real-time Synchronization</h3>
						<p>Ensure all team members see the same information simultaneously, enhancing coordination and
						   decision-making.</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section
			id="objective-tracking"
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-100 flex items-center justify-between">
		<div class="flex flex-col lg:flex-row items-center w-full px-4 sm:px-8 md:px-12 lg:px-20 gap-8">
			<div class="w-full lg:w-1/2 order-2 lg:order-1">
				<div class="space-y-4 md:space-y-8">
					<h2 class="text-3xl md:text-4xl font-bold">
						<span class="text-zinc-900">Objective</span>
						<span class="text-primary-500">Tracking</span>
					</h2>
					<p class="">Track mission-critical objectives in real time with our comprehensive tracking system.
					            Monitor progress, assign responsibilities, and ensure accountability across your entire
					            operation.</p>
				</div>

				<ul class="mt-4 p-4 space-y-4 md:space-y-8">
					<li class="list-disc">
						<span class="font-semibold">Goal-Based Framework:</span>
						<span>Define clear, measurable objectives with customizable success criteria and timeline tracking.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Task Assignment & Accountability:</span>
						<span>Delegate responsibilities to team members with transparent tracking of completion status.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Progress Visualization:</span>
						<span>View real-time progress through intuitive dashboards and reports that highlight achievements and bottlenecks.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Outcome Analysis:</span>
						<span>Evaluate performance against objectives with comprehensive analytics to improve future operations.</span>
					</li>
				</ul>
			</div>
			<div class="w-full lg:w-1/2 flex justify-center lg:justify-end order-1 lg:order-2">
				<div class="w-full rounded-lg flex items-center justify-center">
					<img
							src="{{ Vite::asset('resources/images/objectives.png') }}"
							alt="Objective Tracking"
							onerror="this.onerror=null; this.src=''; this.alt='Objective Tracking Image Placeholder'; this.parentElement.classList.add('flex', 'items-center', 'justify-center');"
					>
					<span class="hidden text-xl sm:text-2xl md:text-3xl text-zinc-100">Objective Tracking Image Placeholder</span>
				</div>
			</div>
		</div>
	</section>

	<section
			id="advanced-reporting"
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-800 relative overflow-hidden">
		<!-- Large background image container -->
		<div class="absolute inset-0 z-0 opacity-90">
			<img
					src="{{ Vite::asset('resources/images/dashboard.png') }}"
					alt="Advanced Reporting"
					class="w-full h-full object-cover object-center"
			/>
			<div class="absolute inset-0 bg-zinc-900 opacity-40"></div>
		</div>

		<!-- Content overlay -->
		<div class="relative z-10 flex flex-col w-full h-full px-4 sm:px-8 md:px-12 lg:px-20 py-8 md:py-12">
			<!-- Main image showcase - larger and more prominent -->
			<div class="w-full flex justify-center mb-10 md:mb-14 mt-4 md:mt-8">
				<div class="w-full max-w-6xl rounded-lg overflow-hidden shadow-2xl border-3 border-primary-500 transform transition-all duration-500 hover:scale-[1.02] relative">
					<!-- Subtle glow effect -->
					<div class="absolute -inset-1 bg-primary-500 opacity-20 blur-lg rounded-lg"></div>
					<img
							src="{{ Vite::asset('resources/images/dashboard.png') }}"
							alt="Advanced Reporting Dashboard"
							class="w-full h-auto relative z-10"
					/>
				</div>
			</div>

			<!-- Text content in a more subtle semi-transparent card -->
			<div class="w-full max-w-5xl mx-auto bg-zinc-800 bg-opacity-70 rounded-lg p-6 md:p-8 backdrop-blur-sm border border-zinc-700">
				<div class="space-y-4 md:space-y-6">
					<h2 class="text-3xl md:text-4xl font-bold text-center mb-4">
						<span class="text-zinc-100">Advanced</span>
						<span class="text-primary-500">Reporting</span>
					</h2>
					<p class="text-zinc-100 text-center max-w-3xl mx-auto">Transform raw data into actionable
					                                                       intelligence with our comprehensive reporting
					                                                       tools. Generate detailed insights and
					                                                       visualizations to support strategic
					                                                       decision-making.</p>
				</div>

				<div class="mt-6 md:mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 text-white">
					<div class="bg-zinc-800 bg-opacity-70 p-4 rounded-lg">
						<h3 class="font-semibold text-primary-400 mb-2">Customizable Dashboards</h3>
						<p>Create personalized views with drag-and-drop widgets to monitor the metrics that matter most
						   to your operation.</p>
					</div>
					<div class="bg-zinc-800 bg-opacity-70 p-4 rounded-lg">
						<h3 class="font-semibold text-primary-400 mb-2">Data Visualization</h3>
						<p>Convert complex data into clear, intuitive charts and graphs that reveal patterns and trends
						   at a glance.</p>
					</div>
					<div class="bg-zinc-800 bg-opacity-70 p-4 rounded-lg">
						<h3 class="font-semibold text-primary-400 mb-2">Automated Reporting</h3>
						<p>Schedule and distribute reports to stakeholders with no manual effort, ensuring everyone
						   stays informed.</p>
					</div>
					<div class="bg-zinc-800 bg-opacity-70 p-4 rounded-lg">
						<h3 class="font-semibold text-primary-400 mb-2">Advanced Analytics</h3>
						<p>Leverage predictive modeling and trend analysis to anticipate needs and optimize resource
						   allocation.</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section
			id="hostage-tracking"
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-100 flex items-center justify-between">
		<div class="flex flex-col lg:flex-row items-center w-full px-4 sm:px-8 md:px-12 lg:px-20 gap-8">
			<div class="w-full lg:w-1/2 order-2 lg:order-1">
				<div class="space-y-4 md:space-y-8">
					<h2 class="text-3xl md:text-4xl font-bold">
						<span class="text-zinc-900">Hostage</span>
						<span class="text-primary-500">Tracking</span>
					</h2>
					<p class="">Monitor and manage hostage situations with our comprehensive tracking system. Keep
					            detailed records of all individuals involved, track negotiations, and coordinate
					            response efforts efficiently.</p>
				</div>

				<ul class="mt-4 p-4 space-y-4 md:space-y-8">
					<li class="list-disc">
						<span class="font-semibold">Comprehensive Profile Management:</span>
						<span>Maintain detailed profiles of all individuals involved, including hostages, suspects, and response personnel.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Status Monitoring & Updates:</span>
						<span>Track real-time status changes, medical conditions, and location information for all hostages.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Negotiation Timeline:</span>
						<span>Document all interactions, demands, and concessions in a chronological timeline for situational awareness.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Resource Allocation:</span>
						<span>Coordinate personnel, equipment, and support services with integrated resource management tools.</span>
					</li>
				</ul>
			</div>
			<div class="w-full lg:w-1/2 flex justify-center lg:justify-end order-1 lg:order-2">
				<div class="w-full space-y-4">
					<div class="rounded-lg overflow-hidden shadow-lg border border-zinc-300">
						<img
								src="{{ Vite::asset('resources/images/hostages-board.png') }}"
								alt="Hostage Tracking Board"
								class="w-full h-auto"
								onerror="this.onerror=null; this.src=''; this.alt='Hostage Tracking Board Placeholder'; this.parentElement.classList.add('flex', 'items-center', 'justify-center', 'bg-zinc-200', 'h-[200px]');"
						>
					</div>
					<div class="rounded-lg overflow-hidden shadow-lg border border-zinc-300">
						<img
								src="{{ Vite::asset('resources/images/hostages-cards.png') }}"
								alt="Hostage Tracking Cards"
								class="w-full h-auto"
								onerror="this.onerror=null; this.src=''; this.alt='Hostage Tracking Cards Placeholder'; this.parentElement.classList.add('flex', 'items-center', 'justify-center', 'bg-zinc-200', 'h-[200px]');"
						>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section
			id="negotiation-tracking"
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-800 flex items-center justify-between">
		<div class="flex flex-col lg:flex-row items-center w-full px-4 sm:px-8 md:px-12 lg:px-20 gap-8">
			<div class="w-full lg:w-1/2 order-2 lg:order-1">
				<div class="space-y-4 md:space-y-8">
					<h2 class="text-3xl md:text-4xl font-bold">
						<span class="text-zinc-100">Negotiation</span>
						<span class="text-primary-500">Tracking</span>
					</h2>
					<p class="dark:text-zinc-100 text-zinc-100">Manage complex negotiations with precision and insight.
					                                            Our negotiation tracking system provides comprehensive
					                                            tools for planning, executing, and analyzing high-stakes
					                                            conversations.</p>
				</div>

				<ul class="mt-4 p-4 space-y-4 md:space-y-8 text-white">
					<li class="list-disc">
						<span class="font-semibold">Strategic Planning Framework:</span>
						<span>Develop structured negotiation strategies with customizable templates and scenario planning tools.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Real-time Conversation Analysis:</span>
						<span>Track key discussion points, demands, and concessions as they occur to maintain situational awareness.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Progress Visualization:</span>
						<span>Monitor negotiation progress through intuitive visual indicators and milestone tracking.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Outcome Documentation:</span>
						<span>Capture and analyze negotiation results with comprehensive reporting and lessons-learned capabilities.</span>
					</li>
				</ul>
			</div>
			<div class="w-full lg:w-1/2 flex justify-center lg:justify-end order-1 lg:order-2">
				<div class="rounded-lg flex items-center justify-center">
					<img
							src="{{ Vite::asset('resources/images/negotiations.png') }}"
							alt="Negotiation Tracking"
							class=""
							onerror="this.onerror=null; this.src=''; this.alt='Negotiation Tracking Image Placeholder'; this.parentElement.classList.add('flex', 'items-center', 'justify-center');"
					>
					<span class="hidden text-xl sm:text-2xl md:text-3xl text-zinc-100">Negotiation Tracking Image Placeholder</span>
				</div>
			</div>
		</div>
	</section>

	<footer class="bg-zinc-100 pt-12 pb-6">
		<div class="container mx-auto px-4 sm:px-8 md:px-12 lg:px-20">
			<!-- Footer top section with logo, links and info -->
			<div class="flex flex-col md:flex-row justify-between items-center md:items-start mb-8 gap-8">
				<!-- Logo and tagline -->
				<div class="flex flex-col items-center md:items-start space-y-4 md:w-1/3">
					<x-logos.app-logo-icon />
					<p class="text-zinc-600 text-center md:text-left">Innovative solutions for high-stakes
					                                                  conversations.</p>
				</div>

				<!-- Quick links -->
				<div class="flex flex-col items-center md:items-start space-y-4 md:w-1/3">
					<h3 class="text-lg font-bold text-zinc-900">Resources</h3>
					<ul class="space-y-2 text-center md:text-left">
						<li><a
									href="/docs"
									class="text-primary-600 hover:text-primary-800 transition-colors">Documentation</a>
						</li>
						<li><a
									href="/docs/getting-started"
									class="text-primary-600 hover:text-primary-800 transition-colors">Getting
						                                                                              Started</a></li>
						<li><a
									href="/docs/user-guide/overview"
									class="text-primary-600 hover:text-primary-800 transition-colors">User Guide</a>
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
						<li><a
									href="tel:+18005551234"
									class="text-primary-600 hover:text-primary-800 transition-colors">1-800-555-1234</a>
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
