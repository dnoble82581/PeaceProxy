<?php

	use Livewire\Attributes\Layout;
	use Livewire\Attributes\Title;
	use Livewire\Volt\Component;

	new #[Layout ('layouts.guest')] #[Title('Welcome - Peace Proxy')] class extends Component {}

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
						target="_blank"
						class="hover:text-gray-400 font-semibold"
						icon="arrow-top-right-on-square"
						href="https://github.com/dnoble82581/PeaceProxy">Git Hub
				</x-link>
				<x-link
						color="secondary"
						target="_blank"
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
							class="rounded-lg"
							src="{{ Vite::asset('resources/images/chat-light.png') }}"
							alt="Image of chat interface">
				</div>
			</div>
		</div>
	</section>

	<section
			id="mood-tracking"
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-100 dark:bg-dark-900 flex items-center">
		<div class="flex flex-col lg:flex-row items-center w-full px-4 sm:px-8 md:px-12 lg:px-20 gap-8">
			<div class="w-full lg:w-1/2 order-2 lg:order-2">
				<div class="space-y-4 md:space-y-8">
					<h2 class="text-3xl md:text-4xl font-bold">
						<span class="text-zinc-900 dark:text-white">Mood</span>
						<span class="text-primary-500">Tracking</span>
					</h2>
					<p class="text-dark-800 dark:text-white">Our integrated mood tracking feature uses advanced voice
					                                         analysis and
					                                         behavioral cues to
					                                         generate a real-time line chart that reflects the subject’s
					                                         emotional state
					                                         over the
					                                         course of the interaction. This visual tool supports
					                                         negotiators and
					                                         command staff by
					                                         providing critical context during dynamic
					                                         conversations.</p>
				</div>

				<ul class="mt-4 p-4 space-y-4 md:space-y-8 text-dark-800 dark:text-white">
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
							class="rounded-lg"
							src="{{ Vite::asset('resources/images/mood-tracking.png') }}"
							alt="Image of Mood Tracking">
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
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-100 dark:bg-dark-900 flex items-center justify-between">
		<div class="flex flex-col lg:flex-row items-center w-full px-4 sm:px-8 md:px-12 lg:px-20 gap-8">
			<div class="w-full lg:w-1/2 order-2 lg:order-1">
				<div class="space-y-4 md:space-y-8">
					<h2 class="text-3xl md:text-4xl font-bold">
						<span class="text-zinc-900 dark:text-white">Objective</span>
						<span class="text-primary-500">Tracking</span>
					</h2>
					<p class="text-dark-800 dark:text-white">Track mission-critical objectives in real time with our
					                                         comprehensive
					                                         tracking system.
					                                         Monitor progress, assign responsibilities, and ensure
					                                         accountability across
					                                         your entire
					                                         operation.</p>
				</div>

				<ul class="mt-4 p-4 space-y-4 md:space-y-8 text-dark-800 dark:text-white">
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
							class="rounded-lg"
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
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-100 dark:bg-dark-900 flex items-center justify-between">
		<div class="flex flex-col lg:flex-row items-center w-full px-4 sm:px-8 md:px-12 lg:px-20 gap-8">
			<div class="w-full lg:w-1/2 order-2 lg:order-1">
				<div class="space-y-4 md:space-y-8">
					<h2 class="text-3xl md:text-4xl font-bold">
						<span class="text-zinc-900 dark:text-white">Hostage</span>
						<span class="text-primary-500">Tracking</span>
					</h2>
					<p class="text-dark-800 dark:text-white">Monitor and manage hostage situations with our
					                                         comprehensive tracking
					                                         system. Keep
					                                         detailed records of all individuals involved, track
					                                         negotiations, and
					                                         coordinate
					                                         response efforts efficiently.</p>
				</div>

				<ul class="mt-4 p-4 space-y-4 md:space-y-8 text-dark-800 dark:text-white">
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
							class="rounded-lg"
							onerror="this.onerror=null; this.src=''; this.alt='Negotiation Tracking Image Placeholder'; this.parentElement.classList.add('flex', 'items-center', 'justify-center');"
					>
					<span class="hidden text-xl sm:text-2xl md:text-3xl text-zinc-100">Negotiation Tracking Image Placeholder</span>
				</div>
			</div>
		</div>
	</section>

	<section
			id="phone-integration"
			class="py-12 md:py-16 lg:py-20 min-h-screen bg-zinc-100 dark:bg-dark-900 flex items-center justify-between">
		<div class="flex flex-col lg:flex-row items-center w-full px-4 sm:px-8 md:px-12 lg:px-20 gap-8">
			<div class="w-full lg:w-1/2 order-2 lg:order-2">
				<div class="space-y-4 md:space-y-8">
					<h2 class="text-3xl md:text-4xl font-bold">
						<span class="text-zinc-900 dark:text-white">Phone</span>
						<span class="text-primary-500">Integration (Comming Soon...)</span>
					</h2>
					<p class="text-dark-800 dark:text-white">Seamlessly integrate phone communications into your crisis
					                                         management
					                                         workflow. Our
					                                         platform provides robust telephony features designed
					                                         specifically for
					                                         high-stakes
					                                         negotiations and emergency response scenarios.</p>
				</div>

				<ul class="mt-4 p-4 space-y-4 md:space-y-8 text-dark-800 dark:text-white">
					<li class="list-disc">
						<span class="font-semibold">Secure Call Routing:</span>
						<span>Direct incoming calls to the appropriate team members with role-based routing and call queuing capabilities.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Call Recording & Transcription:</span>
						<span>Automatically record and transcribe all communications for documentation, analysis, and training purposes.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Multi-line Management:</span>
						<span>Handle multiple communication channels simultaneously with integrated call management tools.</span>
					</li>
					<li class="list-disc">
						<span class="font-semibold">Voice Analysis:</span>
						<span>Leverage advanced voice analysis to detect emotional cues and stress indicators during critical conversations.</span>
					</li>
				</ul>
			</div>
			<div class="w-full lg:w-1/2 flex justify-center lg:justify-start order-1 lg:order-1">
				<div class="h-[300px] sm:h-[400px] md:h-[500px] lg:h-[600px] w-full max-w-[500px] lg:max-w-[800px] rounded-lg flex items-center justify-center dark:bg-dark-800 p-3">
					<img
							src="https://peace-proxy-pro.s3.us-east-1.amazonaws.com/public/assets/phone-example.webp"
							alt="Phone Integration"
							class="rounded-lg"
							onerror="this.onerror=null; this.src=''; this.alt='Phone Integration Image Placeholder'; this.parentElement.classList.add('flex', 'items-center', 'justify-center');"
					>
				</div>
			</div>
		</div>
	</section>
	<section id="the-problem">
		<div class="bg-dark-800 py-24 sm:py-32">
			<div class="mx-auto max-w-7xl px-6 lg:px-8">
				<div class="mx-auto max-w-2xl text-center">
					<h2 class="text-3xl md:text-4xl font-bold">
						<span class="capitalize text-white">Insights </span>
						<span class="text-primary-500 capitalize">That Matter</span>
					</h2>

				</div>
				<div class="mx-auto mt-16 flow-root max-w-2xl sm:mt-20 lg:mx-0 lg:max-w-none">
					<div class="-mt-8 sm:-mx-4 sm:columns-2 sm:text-[0] lg:columns-3">
						<div class="pt-8 sm:inline-block sm:w-full sm:px-4">
							<figure class="rounded-2xl bg-gray-50 p-8 text-sm/6 dark:bg-white/2.5">
								<blockquote class="text-gray-900 dark:text-gray-100">
									<p>“The dynamics of crisis situations typically encountered by hostage negotiators
									   are made more dangerous by fragmented communication channels between tactical,
									   negotiation, and command elements.”</p>
								</blockquote>
								<figcaption class="mt-6 flex items-center gap-x-4">
									<img
											src="https://peace-proxy-pro.s3.us-east-1.amazonaws.com/public/assets/Citation+Images/Amy+Grubb+copy.webp"
											alt=""
											class="size-10 rounded-full bg-gray-50 dark:bg-gray-800" />
									<div>
										<x-link
												class="font-semibold"
												href="https://www.academia.edu/1498232/modern_day_hostage_negotiation_the_evolution_of_an_art_form_within_the_policing_arena">
											Dr Amy Rose
											Grubb
										</x-link>

										<x-link
												href="https://www.glos.ac.uk/staff/profile/amy-grubb/"
												class="block">
											University of Gloucestershire
										</x-link>
									</div>
								</figcaption>
							</figure>
						</div>
						<div class="pt-8 sm:inline-block sm:w-full sm:px-4">
							<figure class="rounded-2xl bg-gray-50 p-8 text-sm/6 dark:bg-white/2.5">
								<blockquote class="text-gray-900 dark:text-gray-100">
									<p>“Anim sit consequat culpa commodo eu do nisi commodo ut aute aliqua. Laborum esse
									   duis tempor consectetur officia mollit fugiat. Exercitation qui elit minim minim
									   quis fugiat ex.”</p>
								</blockquote>
								<figcaption class="mt-6 flex items-center gap-x-4">
									<img
											src="https://peace-proxy-pro.s3.us-east-1.amazonaws.com/public/assets/Citation+Images/Gregory+M+Vecchi+copy.webp"
											alt=""
											class="inline-block size-8 rounded-full ring-2 ring-white outline -outline-offset-1 outline-black/5 dark:ring-gray-900 dark:outline-white/10" />

									<div>
										<x-link
												class="font-semibold"
												href="https://www.sciencedirect.com/science/article/abs/pii/S1359178904000758?via%3Dihub">
											Gregory M. Vecchi...
										</x-link>
										<x-link href="https://www.sciencedirect.com/science/article/abs/pii/S1359178904000758?via%3Dihub">
											Aggression and Violent Behavior
										</x-link>
									</div>
								</figcaption>
							</figure>
						</div>
						<div class="pt-8 sm:inline-block sm:w-full sm:px-4">
							<figure class="rounded-2xl bg-gray-50 p-8 text-sm/6 dark:bg-white/2.5">
								<blockquote class="text-gray-900 dark:text-gray-100">
									<p>“Police officers in hostage situations consistently reported that communication
									   breakdowns with other units created greater uncertainty than the behavior of the
									   hostage taker.”</p>
								</blockquote>
								<figcaption class="mt-6 flex items-center gap-x-4">
									<img
											src="https://peace-proxy-pro.s3.us-east-1.amazonaws.com/public/assets/Citation+Images/Nicola+Power.webp"
											alt=""
											class="size-10 rounded-full bg-gray-50 dark:bg-gray-800" />
									<div class="flex flex-col">
										<x-link
												class="font-semibold"
												href="https://www.researchgate.net/publication/257480359_Coping_with_uncertainty_Police_strategies_for_resilient_decision-making_and_action_implementation">
											Nicola Power
										</x-link>
										<x-link href="https://www.researchgate.net/institution/Lancaster-University?_tp=eyJjb250ZXh0Ijp7ImZpcnN0UGFnZSI6InB1YmxpY2F0aW9uIiwicGFnZSI6InB1YmxpY2F0aW9uIn19">
											Lancaster University
										</x-link>
									</div>
								</figcaption>
							</figure>
						</div>
						<div class="pt-8 sm:inline-block sm:w-full sm:px-4">
							<figure class="rounded-2xl bg-gray-50 p-8 text-sm/6 dark:bg-white/2.5">
								<blockquote class="text-gray-900 dark:text-gray-100">
									<p>“Many negotiation failures can be traced not to the negotiator’s skill, but to
									   poor coordination and delayed communication with decision-makers outside the
									   immediate scene.”</p>
								</blockquote>
								<figcaption class="mt-6 flex items-center gap-x-4">
									<img
											src="https://peace-proxy-pro.s3.us-east-1.amazonaws.com/public/assets/Citation+Images/Dr.+Matthew+Logan+copy.webp"
											alt=""
											class="size-10 rounded-full bg-gray-50 dark:bg-gray-800" />
									<div>
										<x-link
												href="file:///Users/dustynoble/Downloads/ubc_2001-714985.pdf-1.pdf"
												class="font-semibold">
											Matthew H. Logan, Ph.D.
										</x-link>
										<x-link href="https://www.mattloganhalo.com/">
											Forensic Behavioural Specialist
										</x-link>
									</div>
								</figcaption>
							</figure>
						</div>
						<div class="pt-8 sm:inline-block sm:w-full sm:px-4">
							<figure class="rounded-2xl bg-gray-50 p-8 text-sm/6 dark:bg-white/2.5">
								<blockquote class="text-gray-900 dark:text-gray-100">
									<p>“Without a clear, unified channel, negotiators often found themselves ‘working
									   blind,’ unaware of tactical or command shifts that altered the negotiation
									   dynamic.”</p>
								</blockquote>
								<figcaption class="mt-6 flex items-center gap-x-4">
									<img
											src="https://peace-proxy-pro.s3.us-east-1.amazonaws.com/public/assets/Citation+Images/Terry+Royce+copy.webp"
											alt=""
											class="size-10 rounded-full bg-gray-50 dark:bg-gray-800" />
									<div class="flex flex-col">
										<x-link
												href="https://watermark02.silverchair.com/j.1571-9979.2005.00045.x.pdf?token=AQECAHi208BE49Ooan9kkhW_Ercy7Dm3ZL_9Cf3qfKAc485ysgAAA0owggNGBgkqhkiG9w0BBwagggM3MIIDMwIBADCCAywGCSqGSIb3DQEHATAeBglghkgBZQMEAS4wEQQM2L0cjzn8g0EsLy3nAgEQgIIC_RnDHiWnHmFkiP-AQqdlY77-9W0H6MSN7M-ubtJDRFLbRWRvsq2Iiget3B7DR9twHUK711G8k5bmNpvBb5Yuc4Z7oC4rzvjyCZ845K8l5M1LYRLvviMTyTLXPWa44RMy2lFlVQKgosY04CT0GMcaThXz7N3AZQja4taH9Fh9dUV1pZGNHxmr5WwAk6qIrN0p10KM22FmZxD0_piwcC1S-4o9UnbZzYL2X1fXMSNiw0PNQnvY7-FmyiUaonaItDfqTYKUGDwv-aaoJt09W7GL8rKNsbtAAIqoQnZ_j8hvaTMOS5IFo3-a2iK-FrgaqeEtiW7SMpEe5Wf5p11SVbCWQDKMLLg3TzmpecZXbchWD0PCJSeWiUhOO3ubRSlGJ92oljq-KEnYn9lwAtuMxKdy6gXdfEJux3_YagL9TetU143rfgO1p1si2A4hMRb3sSHX0O6ezyVVPtw1NerOfgSSLtTGGjHxDKHhMIaRind05GhmbIKJNUvt4BA1kNgnEVfBEl23dvJ2CGyvhSiIVEZGzl2Xto9eOP6ob6HQbghDIQywf1vjzg_auqm_dAjKdDY6CFIiKOKvv0csezqusiAzdciRT9Hfs394gScaxgF5DHcdAt3R3R5IkuN_NshxLmpAdMOhEB0LJWzUhSOP_lH4QuPc0IQZM6XawKzScFRaTMnv3vrdA4oLO7oZfB_-kNMbeCQBXAixXDhFf-lLWWneJOm7SL8HRg2rGhAvqxuAtH6ZupzJHrG7_BfFIfvPBAExnfezHABJSCumPUVNIJETe48bqdWCz8SrN8Ttm_pJsaROqjfQyqDwCZaNdbSyFT5TgcKacwEf8Uv5m7zK_NPcZFzuO0v517XymzELPogSVd3WgfjVhZwrBiJztsfZXoHi_Ws1qy6Eea49fIdCskAcZn7CLXkCeYRBP7UuCGDxqJUP5KVlfjDXh2s7XY_GBR1Hb8rhGB6bH7hgMlnnqII_LfGVquXns5Uhw60Z5Zy-RjnSNykKIxKUzugaksPOWQ"
												class="font-semibold">
											Terry Royce
										</x-link>
										<x-link href="https://profiles.uts.edu.au/Terry.Royce">
											University of Technology Sydney
										</x-link>

									</div>
								</figcaption>
							</figure>
						</div>
						<div class="pt-8 sm:inline-block sm:w-full sm:px-4">
							<figure class="rounded-2xl bg-gray-50 p-8 text-sm/6 dark:bg-white/2.5">
								<blockquote class="text-gray-900 dark:text-gray-100">
									<p>“The greatest enemy of crisis resolution is not the perpetrator but the confusion
									   within our own ranks when communication and intelligence are not integrated.”</p>
								</blockquote>
								<figcaption class="mt-6 flex items-center gap-x-4">
									<img
											src="https://peace-proxy-pro.s3.us-east-1.amazonaws.com/public/assets/Citation+Images/Thomas+Strentz+copy.webp"
											alt=""
											class="size-10 rounded-full bg-gray-50 dark:bg-gray-800" />
									<div>
										<x-link
												class="font-semibold"
												href="https://public.ntoa.org/AppResources/publications/Articles/2289.pdf">
											Thomas Strentz, Ph.D.
										</x-link>
										<x-link href="https://www.ntoa.org/people/thomas-strentz">Co-Founder, FBI
										                                                          Behavioral Science
										                                                          Unit
										</x-link>
									</div>
								</figcaption>
							</figure>
						</div>
						<div class="pt-8 sm:inline-block sm:w-full sm:px-4">
							<figure class="rounded-2xl bg-gray-50 p-8 text-sm/6 dark:bg-white/2.5">
								<blockquote class="text-gray-900 dark:text-gray-100">
									<p>“Communication breakdowns with local unit commanders frequently led to
									   operational errors, highlighting the need for integrated platforms that cross
									   organizational boundaries.”</p>
								</blockquote>
								<figcaption class="mt-6 flex items-center gap-x-4">
									<img
											src="https://peace-proxy-pro.s3.us-east-1.amazonaws.com/public/assets/Citation+Images/Chris+Glacomantonio+copy.webp"
											alt=""
											class="size-10 rounded-full bg-gray-50 dark:bg-gray-800" />
									<div>
										<x-link
												class="font-semibold"
												href="https://www.tandfonline.com/doi/abs/10.1080/10439463.2013.784302">
											Chris Glacomantonio
										</x-link>
										<x-link href="#">
											Centre for Criminology, University of Oxford
										</x-link>
									</div>
								</figcaption>
							</figure>
						</div>
					</div>
				</div>
			</div>
		</div>

	</section>

	<!-- Support The Project -->
	<section
			id="support-the-project"
			class="bg-white dark:bg-dark-900 py-16 sm:py-24">
		<div class="container mx-auto px-4 sm:px-8 md:px-12 lg:px-20 max-w-7xl">
			<div class="mx-auto max-w-3xl text-center">
				<h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
					We're building this in the open — and it's still moving
				</h2>
				<p class="mt-4 text-gray-600 dark:text-dark-300">
					Peace Proxy is an ongoing project. If this mission resonates with you and you'd like to help it move
					faster,
					your support directly funds development, infrastructure, and research.
				</p>
			</div>

			@if(session('ok') || session('info') || session('error'))
				<div class="mx-auto max-w-2xl mt-8">
					<div
							class="rounded-lg border p-4"
							@class([
							   'bg-green-50 border-green-200 text-green-800' => session('ok'),
							   'bg-blue-50 border-blue-200 text-blue-800' => session('info'),
							   'bg-red-50 border-red-200 text-red-800' => session('error'),
							])>
						<p class="text-sm">
							{{ session('ok') ?? session('info') ?? session('error') }}
						</p>
					</div>
				</div>
			@endif

			<div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
				<div class="bg-zinc-50 dark:bg-dark-800 rounded-xl p-6 sm:p-8">
					<h3 class="text-xl font-semibold text-gray-900 dark:text-white">Why this matters</h3>
					<ul class="mt-4 space-y-3 text-gray-700 dark:text-dark-200 text-sm">
						<li class="list-disc ml-4">Faster feature delivery for teams using Peace Proxy in the field</li>
						<li class="list-disc ml-4">Sustained research into negotiation workflows and comms reliability
						</li>
						<li class="list-disc ml-4">Infrastructure and security to keep the platform stable under
						                           pressure
						</li>
					</ul>
					<p class="mt-4 text-xs text-gray-500 dark:text-dark-400">No paywalls. No gimmicks. Just
					                                                         progress—made possible by people who
					                                                         believe this should exist.</p>
				</div>

				<div
						class="bg-white dark:bg-dark-800 rounded-xl p-6 sm:p-8 border border-zinc-200 dark:border-dark-700"
						x-data="{ amount: 25, preset(a){ this.amount = a }, isActive(a){ return Number(this.amount) === Number(a) } }">
					<h3 class="text-xl font-semibold text-gray-900 dark:text-white">Support development</h3>
					<form
							method="POST"
							action="{{ route('donations.checkout') }}"
							class="mt-6 space-y-6">
						@csrf
						<input
								type="hidden"
								name="amount"
								:value="amount">

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-dark-200">Choose an
							                                                                          amount</label>
							<div class="mt-3 grid grid-cols-4 gap-2">
								<button
										type="button"
										@click="preset(5)"
										:class="isActive(5) ? 'bg-primary-600 text-white' : 'bg-zinc-100 dark:bg-dark-700 text-gray-800 dark:text-dark-200'"
										class="px-3 py-2 rounded-lg text-sm font-semibold">$5
								</button>
								<button
										type="button"
										@click="preset(10)"
										:class="isActive(10) ? 'bg-primary-600 text-white' : 'bg-zinc-100 dark:bg-dark-700 text-gray-800 dark:text-dark-200'"
										class="px-3 py-2 rounded-lg text-sm font-semibold">$10
								</button>
								<button
										type="button"
										@click="preset(25)"
										:class="isActive(25) ? 'bg-primary-600 text-white' : 'bg-zinc-100 dark:bg-dark-700 text-gray-800 dark:text-dark-200'"
										class="px-3 py-2 rounded-lg text-sm font-semibold">$25
								</button>
								<button
										type="button"
										@click="preset(50)"
										:class="isActive(50) ? 'bg-primary-600 text-white' : 'bg-zinc-100 dark:bg-dark-700 text-gray-800 dark:text-dark-200'"
										class="px-3 py-2 rounded-lg text-sm font-semibold">$50
								</button>
							</div>
						</div>

						<div>
							<label
									for="custom-amount"
									class="block text-sm font-medium text-gray-700 dark:text-dark-200">Or enter a custom
							                                                                           amount</label>
							<div class="mt-2 flex rounded-lg overflow-hidden border border-zinc-200 dark:border-dark-600">
								<span class="px-3 py-2 bg-zinc-100 dark:bg-dark-700 text-gray-700 dark:text-dark-300">$</span>
								<input
										id="custom-amount"
										type="number"
										min="1"
										step="1"
										x-model.number="amount"
										class="w-full px-3 py-2 bg-white dark:bg-dark-800 text-gray-900 dark:text-white focus:outline-none"
										placeholder="25" />
							</div>
							<p class="mt-2 text-xs text-gray-500 dark:text-dark-400">One-time donation
							                                                         in {{ strtoupper(config('cashier.currency', 'usd')) }}
							                                                         .</p>
						</div>

						<div class="flex justify-end">
							<button
									type="submit"
									class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 focus:outline-none">
								<x-icon
										name="heart"
										class="size-4" />
								Donate
							</button>
						</div>
					</form>
					<p class="mt-4 text-xs text-gray-500 dark:text-dark-400">Processed securely by Stripe. We do not
					                                                         store your card details.</p>
				</div>
			</div>
		</div>
	</section>

	<footer class="bg-zinc-100 dark:bg-dark-900 pt-12 pb-6">
		<div class="container mx-auto px-4 sm:px-8 md:px-12 lg:px-20">
			<!-- Footer top section with logo, links and info -->
			<div class="flex flex-col md:flex-row justify-between items-center md:items-start mb-8 gap-8">
				<!-- Logo and tagline -->
				<div class="flex flex-col items-center md:items-start space-y-4 md:w-1/3">
					<x-logos.app-logo-icon />
					{{--					<p class="text-zinc-600 text-center md:text-left">Innovative solutions for high-stakes--}}
					{{--					                                                  conversations.</p>--}}
				</div>

				<!-- Quick links -->
				<div class="flex flex-col items-center md:items-start space-y-4 md:w-1/3">
					<h3 class="text-lg font-bold text-zinc-900 dark:text-white">Resources</h3>
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
					<h3 class="text-lg font-bold text-zinc-900 dark:text-white">Contact Us</h3>
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
				<div class="flex flex-col items-center md:items-start space-y-4 md:w-1/3">
					<h3 class="text-lg font-bold text-zinc-900 dark:text-white">About Us</h3>
					<ul class="space-y-2 text-center md:text-left">
						<li><a
									href="{{ route('about') }}"
									class="text-primary-600 hover:text-primary-800 transition-colors">Meet The
						                                                                              Creator</a>
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
