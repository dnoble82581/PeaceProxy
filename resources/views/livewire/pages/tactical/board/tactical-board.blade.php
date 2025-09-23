<?php

	use Livewire\Volt\Component;

	new class extends Component {
		public int $negotiationId;
		public string $tab = 'maps';

		public function mount($negotiationId):void
		{
			$this->negotiationId = (int) $negotiationId;
		}

		public function setActiveTab(string $tab):void
		{
			$this->tab = $tab;
		}
	};

?>

<div
		wire:key="tactical-board-root"
		x-data="{ tab: @entangle('tab') }"
		class="dark:bg-dark-800 h-full rounded-lg px-2 pb-2 overflow-y-auto">
	<div class="sticky top-0 z-10">
		<div class="grid grid-cols-1 sm:hidden">
			<select
					x-model="tab"
					aria-label="Select a tab"
					class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-dark-600 py-2 pr-8 pl-3 text-base text-gray-900 dark:text-gray-200 outline-1 -outline-offset-1 outline-gray-300 dark:outline-dark-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary-600">
				<option value="maps">Maps</option>
				<option value="teams">Teams</option>
			</select>
			<svg
					viewBox="0 0 16 16"
					fill="currentColor"
					aria-hidden="true"
					class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end fill-gray-500 dark:fill-dark-300">
				<path
						d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z"
						clip-rule="evenodd"
						fill-rule="evenodd" />
			</svg>
		</div>
		<div class="hidden sm:block bg-white dark:bg-dark-800">
			<div class="border-b border-gray-200 dark:border-dark-400">
				<nav
						aria-label="Tabs"
						class="-mb-px flex space-x-8">
					<button
							@click="tab = 'maps'"
							class="group inline-flex gap-1 items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400"
							:class="{'border-b-primary-500 text-primary-600 dark:text-primary-400': tab === 'maps', 'border-transparent': tab !== 'maps'}">
						<x-icon
								name="map"
								class="size-5" />
						<span>Maps</span>
					</button>
					<button
							@click="tab = 'resources'"
							class="group inline-flex gap-1 items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400"
							:class="{'border-b-primary-500 text-primary-600 dark:text-primary-400': tab === 'resources', 'border-transparent': tab !== 'resources'}">
						<x-icon
								name="users"
								class="size-5" />
						<span>Resources</span>
					</button>
					<button
							@click="tab = 'requests'"
							class="group inline-flex gap-1 items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400"
							:class="{'border-b-primary-500 text-primary-600 dark:text-primary-400': tab === 'requests', 'border-transparent': tab !== 'requests'}">
						<x-icon
								name="users"
								class="size-5" />
						<span>Requests</span>
					</button>
				</nav>
			</div>
		</div>
	</div>

	<div
			class="mt-4 space-y-2"
			x-show="tab === 'maps'">
		<div class="p-4 text-gray-600 dark:text-dark-200">
			<livewire:pages.tactical.board.maps />
		</div>
	</div>

	<div
			class="mt-4"
			x-show="tab === 'resources'">
		<div class="p-4 text-gray-600 dark:text-dark-200">
			<p class="text-sm">Team resources tools will appear here.</p>
			<!-- Placeholder content; plug in your resources component when ready -->
		</div>
	</div>
	<div
			class="mt-4"
			x-show="tab === 'requests'">
		<div class="p-4 text-gray-600 dark:text-dark-200 grid grid-cols-1 gap-4 sm:grid-cols-2">
			<x-card>
				<x-slot:header>
					<h3 class="p-2 font-semibold">Demands</h3>
				</x-slot:header>
				<livewire:pages.tactical.board.demands :negotiation-id="$negotiationId" />
			</x-card>
			<x-card>
				<x-slot:header>
					<h3 class="p-2 font-semibold">RFIs</h3>
				</x-slot:header>
				<livewire:pages.tactical.board.rfis :negotiation-id="$negotiationId" />
			</x-card>
		</div>
	</div>
</div>
