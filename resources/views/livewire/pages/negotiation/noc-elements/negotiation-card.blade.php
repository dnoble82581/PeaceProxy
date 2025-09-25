<?php

	use App\Models\Negotiation;
	use App\Services\Negotiation\NegotiationFetchingService;
	use Livewire\Volt\Component;

	new class extends Component {
		public Negotiation $negotiation;

		public function mount($negotiationId):void
		{
			$this->negotiation = app(NegotiationFetchingService::class)->getNegotiationById($negotiationId);
		}
	}

?>

<div
		class="h-[13rem] border border-gray-300 rounded-lg text-dark-800 dark:text-primary-50 dark:bg-dark-800 px-2 pb-2 overflow-y-auto"
		x-data="{tab: 'general'}">
	<div class="sticky top-0 z-10 bg-white dark:bg-dark-800">
		<div class="grid grid-cols-1 sm:hidden">
			<!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
			<select
					aria-label="Select a tab"
					class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-dark-600 py-2 pr-8 pl-3 text-base text-gray-900 dark:text-gray-200 outline-1 -outline-offset-1 outline-gray-300 dark:outline-dark-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary-600">
				<option>My Account</option>
				<option>Company</option>
				<option selected>Team Members</option>
				<option>Billing</option>
			</select>
			<svg
					viewBox="0 0 16 16"
					fill="currentColor"
					data-slot="icon"
					aria-hidden="true"
					class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end fill-gray-500">
				<path
						d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z"
						clip-rule="evenodd"
						fill-rule="evenodd" />
			</svg>
		</div>
		<div class="hidden sm:block">
			<div class="border-b border-gray-200 dark:border-dark-400">
				<nav
						aria-label="Tabs"
						class="-mb-px flex space-x-8">
					<!-- Current: "border-indigo-500 text-indigo-600", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
					<button
							@click="tab = 'general'"
							:class="{'border-b-primary-500 text-dark-800 dark:text-primary-400': tab === 'general'}"
							class="border-b-1 px-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="user"
								class="h-4 w-4">
							<x-slot:right>
								General
							</x-slot:right>
						</x-icon>
					</button>
					<button
							@click="tab = 'hostages'"
							:class="{'border-b-primary-500 text-dark-800 dark:text-primary-400': tab === 'hostages'}"
							class="border-b-1 px-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="user"
								class="h-4 w-4">
							<x-slot:right>
								Hostages
							</x-slot:right>
						</x-icon>
					</button>
					<button
							@click="tab = 'initial_complaint'"
							:class="{'border-b-primary-500 text-dark-800 dark:text-primary-400': tab === 'initial_complaint'}"
							class="border-b-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="exclamation-triangle"
								class="h-4 w-4">
							<x-slot:right>
								Initial Complaint
							</x-slot:right>
						</x-icon>

					</button>
					<button
							@click="tab = 'summary'"
							:class="{'border-b-primary-500 text-dark-800 dark:text-primary-400': tab === 'summary'}"
							class="border-b-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="heart"
								class="h-4 w-4">
							<x-slot:right>
								Summary
							</x-slot:right>
						</x-icon>

					</button>
					<button
							@click="tab = 'documents'"
							aria-current="page"
							:class="{'border-b-primary-500 text-dark-800 dark:text-primary-400': tab === 'documents'}"
							class="border-b-1 px-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="document-magnifying-glass"
								class="h-4 w-4">
							<x-slot:right>
								Documents
							</x-slot:right>
						</x-icon>
					</button>
				</nav>
			</div>
		</div>
	</div>

	<div
			x-show="tab === 'general'"
			class="pt-2">
		<livewire:pages.negotiation.noc-elements.negotiation.negotiation-general
				:subjectId="$negotiation->primarySubject()->id"
				:negotiationId="$negotiation->id" />
	</div>
	<div
			x-show="tab === 'hostages'"
			class="overflow-visible">
		<livewire:pages.negotiation.noc-elements.negotiation.negotiation-card-hostages :negotiationId="$this->negotiation->id" />
	</div>
	<div
			class="p-4 pt-6"
			x-show="tab === 'initial_complaint'">
		@if($this->negotiation->initial_complaint)
			<p class="text-sm">{{ $this->negotiation->initial_complaint }}</p>
		@else
			<div class="text-center p-4 text-gray-500">
				No initial complaint found for this negotiation.
				<p class="mt-2">
					To add an initial complaint, please edit the negotiation details from the negotiation management
					page.
				</p>
			</div>
		@endif
	</div>
	<div
			class="p-4 pt-6"
			x-show="tab === 'summary'">
		@if($this->negotiation->summary)
			<p class="text-sm">{{ $this->negotiation->summary }}</p>
		@else
			<div class="text-center p-4 text-gray-500">
				No summary information found for this negotiation.
				<p class="mt-2">
					To add a summary, please edit the negotiation details from the negotiation management page.
				</p>
			</div>
		@endif
	</div>
	<div
			x-show="tab === 'documents'"
			class="pt-2">
		<livewire:pages.negotiation.noc-elements.negotiation.negotiation-documents :negotiationId="$this->negotiation->id" />
	</div>
</div>