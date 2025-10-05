<?php

	use App\Models\Negotiation;
	use App\Services\Negotiation\NegotiationFetchingService;
	use Livewire\Volt\Component;

	new class extends Component {
		public int $negotiationId = 0;
		public int $subjectId = 0;
		public string $tab = 'board';

		public function mount($negotiationId)
		{
			$this->negotiationId = (int) ($negotiationId ?? 0);
			$neg = app(NegotiationFetchingService::class)->getNegotiationById($this->negotiationId);
			if ($neg) {
				$primary = $neg->primarySubject();
				$this->subjectId = $primary?->id ?? 0;
			} else {
				$this->subjectId = 0;
			}

		}

		/**
		 * Set the active tab
		 *
		 * @param  string  $tab  The tab to set as active
		 *
		 * @return void
		 */
		public function setActiveTab(string $tab):void
		{
			$this->tab = $tab;
		}
	}

?>

<div
		x-data="{ tab: 'board' }"
		class="dark:bg-dark-800 h-full rounded-lg px-2 pb-2 overflow-y-auto">
	<div class="sticky top-0 z-10">
		<div class="grid grid-cols-1 sm:hidden">
			<select
					aria-label="Select a tab"
					class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-dark-600 py-2 pr-8 pl-3 text-base text-gray-900 dark:text-gray-200 outline-1 -outline-offset-1 outline-gray-300 dark:outline-dark-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary-600">
				<option value="board">Board</option>
				<option value="charts">Charts</option>
				<option value="team">Objectives</option>
				<option value="notes">Notes</option>
			</select>
			<svg
					viewBox="0 0 16 16"
					fill="currentColor"
					data-slot="icon"
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
							@click="tab = 'board'"
							class="group inline-flex gap-1 items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400"
							:class="{'border-b-primary-500 text-primary-600 dark:text-primary-400': tab === 'board', 'border-transparent': tab !== 'board'}"
					>
						<x-icon
								name="clipboard"
								class="size-5" />
						<span>Board</span>
					</button>
					<button
							@click="tab = 'charts'"
							class="group inline-flex gap-1 items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400"
							:class="{'border-b-primary-500 text-primary-600 dark:text-primary-400': tab === 'charts', 'border-transparent': tab !== 'charts'}"
					>
						<x-icon
								name="chart-bar"
								class="size-5" />
						<span>Charts</span>
					</button>
					<button
							@click="tab = 'objectives'"
							class="group inline-flex gap-1 items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400"
							:class="{'border-b-primary-500 text-primary-600 dark:text-primary-400': tab === 'objectives', 'border-transparent': tab !== 'objectives'}"
					>
						<x-icon
								name="exclamation-triangle"
								class="size-5" />
						<span>Objectives</span>
					</button>
					<button
							@click="tab = 'notes'"
							class="group inline-flex gap-1 items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400"
							:class="{'border-b-primary-500 text-primary-600 dark:text-primary-400': tab === 'notes', 'border-transparent': tab !== 'notes'}"
					>
						<x-icon
								name="pencil"
								class="size-5" />
						<span>Notes</span>
					</button>
					<button
							@click="tab = 'rfi'"
							class="group inline-flex gap-1 items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400"
							:class="{'border-b-primary-500 text-primary-600 dark:text-primary-400': tab === 'rfi', 'border-transparent': tab !== 'rfi'}"
					>
						<x-icon
								name="identification"
								class="size-5" />
						<span>RFI</span>
					</button>
					<button
							@click="tab = 'timeline'"
							class="group inline-flex gap-1 items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400"
							:class="{'border-b-primary-500 text-primary-600 dark:text-primary-400': tab === 'timeline', 'border-transparent': tab !== 'timeline'}"
					>
						<x-icon
								name="clock"
								class="size-5" />
						<span>Timeline</span>
					</button>
				</nav>
			</div>
		</div>
	</div>


	<div
			class="mt-4 space-y-2"
			x-show="tab === 'board'">
		<livewire:pages.negotiation.board.hooks :negotiationId="$this->negotiationId" />
		<livewire:pages.negotiation.board.triggers :negotiationId="$this->negotiationId" />
		<livewire:pages.negotiation.board.hostages :negotiationId="$this->negotiationId" />
		<livewire:pages.negotiation.board.demands :negotiationId="$this->negotiationId" />
	</div>

	<div
			class="mt-4"
			x-show="tab === 'charts'">
		<div class="p-4 text-gray-500 dark:text-dark-300">
			<livewire:pages.negotiation.charts.negotiation-charts :negotiationId="$this->negotiationId" />
		</div>
	</div>

	<div
			class="mt-4"
			x-show="tab === 'objectives'">
		<div class="p-4 text-gray-500 dark:text-dark-300">
			<livewire:pages.negotiation.board.objectives :negotiationId="$this->negotiationId" />
		</div>
	</div>

	<div
			class="mt-4"
			x-show="tab === 'notes'">
		<div class="p-4 text-gray-500 dark:text-dark-300">
			<livewire:pages.negotiation.board.notes :negotiationId="$this->negotiationId" />
		</div>
	</div>

	<div
			class="mt-4"
			x-show="tab === 'rfi'">
		<div class="p-4 text-gray-500 dark:text-dark-300">
			<livewire:pages.negotiation.board.rfi :negotiationId="$this->negotiationId" />
		</div>
	</div>
	<div
			class="mt-4"
			x-show="tab === 'timeline'">
		<div class="p-4 text-gray-500 dark:text-dark-300">
			<livewire:pages.negotiation.board.timeline
					:negotiationId="$this->negotiationId"
					:subjectId="$this->subjectId" />
		</div>
	</div>
</div>
