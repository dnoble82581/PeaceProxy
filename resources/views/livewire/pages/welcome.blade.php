<?php

	use Livewire\Attributes\Layout;
	use Livewire\Attributes\Title;
	use Livewire\Volt\Component;

	new #[Layout ('layouts.guest')] #[Title('Welcome')] class extends Component {}

?>

<div>
	<section id="hero">
		<div class="flex px-8 pt-4 items-center justify-between">
			<x-logos.app-logo-icon />

			<div class="uppercase space-x-4">
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
				<div class="uppercase space-x-4">
					<a
							class="hover:text-gray-500"
							href="{{ route('dashboard', ['tenantSubdomain' => tenant()->subdomain]) }}">Dashboard</a>
				</div>
			@else
				<div class="uppercase space-x-4">
					<a
							class="hover:text-gray-500"
							href="{{ route('login') }}">Login</a>
					<a
							class="hover:text-gray-500"
							href="{{ route('register') }}">Register</a>
				</div>
			@endauth
		</div>

		<div class="h-screen flex items-center justify-center">
			<div class="text-center space-y-8">
				<div class="text-6xl font-bold tracking-tight uppercase">
					<span>Innovative</span>
					<span class="text-blue-500">Solutions</span>
				</div>
				<div class="text-5xl uppercase text-zinc-900 tracking-wider dark:text-zinc-100">
					<span>For High-Stakes</span>
					<span class="block mt-2 text-blue-500">Conversations</span>
				</div>
				<div class="mt-8 text-lg">
					<p>Access your organization's portal or create a new one</p>
				</div>
				<div class="space-x-4">
					<a href="{{ route('login') }}">
						<x-button>Login to Your Portal</x-button>
					</a>
					<a href="{{ route('register') }}">
						<x-button color="secondary">Register New Organization</x-button>
					</a>
				</div>
			</div>
		</div>
	</section>

	<section
			id="real-time-communication"
			class="h-screen bg-zinc-800 flex items-center justify-between">
		<div class="flex items-center w-full px-20">
			<div class="flex-1">
				<div class="space-y-8">
					<h2 class="text-4xl font-bold">
						<span class="text-zinc-100">Real-Time</span>
						<span class="text-primary-500">Communication</span>
					</h2>
					<p class="dark:text-zinc-100 text-zinc-100">Lorem ipsum dolor sit amet, consectetur adipisicing
					                                            elit. Asperiores
					                                            deleniti eligendi
					                                            impedit
					                                            incidunt ipsa libero nam odio veritatis voluptate? Amet
					                                            at cupiditate
					                                            dolore esse illo
					                                            non
					                                            obcaecati
					                                            placeat praesentium ut.</p>
				</div>

				<ul class="mt-4 p-4 space-y-8 text-white">
					<li class="list-disc">
						<span class="font-semibold">Command-Level Visibility:</span>
						<span>Monitor active negotiations and field communications in real Ctime, enabling informed decision-making and strategic oversight.</span>
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
			<div class="flex-1 flex justify-end">
				<div class="h-[800px] w-[600px] bg-slate-700 rounded-lg flex items-center justify-center">
					<span class="text-3xl text-zinc-100">600 x 800</span>
				</div>
			</div>
		</div>
	</section>

	<section
			id="mood-tracking"
			class="h-screen bg-zinc-00 flex items-center">
		<div class="flex items-center flex-row-reverse gap-8 w-full px-20">
			<div class="flex-1">
				<div class="space-y-8">
					<h2 class="text-4xl font-bold">
						<span class="text-zinc-900">Mood</span>
						<span class="text-primary-500">Tracking</span>
					</h2>
					<p class="">Our integrated mood tracking feature uses advanced voice analysis and behavioral cues to
					            generate a real-time line chart that reflects the subject’s emotional state over the
					            course of the interaction. This visual tool supports negotiators and command staff by
					            providing critical context during dynamic conversations.</p>
				</div>

				<ul class="mt-4 p-4 space-y-8">
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
			<div class="flex-1 flex justify-start">
				<div class="h-[600px] w-[800px] bg-slate-700 rounded-lg flex items-center justify-center">
					<span class="text-3xl text-zinc-100">500 x 400</span>
				</div>
			</div>
		</div>
	</section>
</div>
