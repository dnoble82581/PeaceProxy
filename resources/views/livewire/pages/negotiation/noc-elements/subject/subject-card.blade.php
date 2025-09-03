<?php

	use App\Models\Negotiation;
	use App\Models\Subject;
	use Livewire\Volt\Component;
	use Illuminate\View\View;


	new class extends Component {
		public Subject $primarySubject;
		public Negotiation $negotiation;

		public function mount($negotiation)
		{
			$this->negotiation = $negotiation;
			$this->primarySubject = $negotiation->primarySubject();
		}

		public function rendering(View $view):void
		{
			$view->layoutData(['negotiation' => $this->negotiation]);
		}

		public function editSubject()
		{
			return $this->redirect(route('subject.edit',
				[
					'subject' => $this->primarySubject,
					'negotiation' => $this->negotiation,
					'tenantSubdomain' => tenant()->subdomain
				]));
		}

		public function viewSubject()
		{
			return $this->redirect(route('subject.show',
				[
					'subject' => $this->primarySubject,
					'negotiation' => $this->negotiation,
					'tenantSubdomain' => tenant()->subdomain
				]));
		}
	}

?>

<div
		class="h-[13rem] border border-gray-300 rounded-lg text-dark-800 dark:text-primary-50 dark:bg-dark-800 px-2 pb-2 overflow-y-auto"
		x-data="{tab: 'general'}">
	<div class="sticky top-0 z-10 dark:bg-dark-900 bg-white">
		<div class="grid grid-cols-1 sm:hidden">
			<select
					aria-label="Select a tab"
					class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-dark-600 py-2 pr-8 pl-3 text-base text-gray-900 dark:text-gray-200 outline-1 -outline-offset-1 outline-gray-300 dark:outline-dark-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary-600">
				<option>General</option>
				<option>Warnings</option>
				<option selected>Health</option>
				<option>Warrants</option>
				<option>Contacts</option>
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
		<div class="hidden sm:block dark:bg-dark-800">
			<div class="border-b border-gray-200 dark:border-dark-400">
				<nav
						aria-label="Tabs"
						class="-mb-px flex space-x-8">
					<button
							@click="tab = 'general'"
							:class="{'border-b-primary-500 text-primary-500 dark:text-primary-400': tab === 'general'}"
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
							@click="tab = 'warnings'"
							:class="{'border-b-primary-500 text-primary-500 dark:text-primary-400': tab === 'warnings'}"
							class="border-b-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="exclamation-triangle"
								class="h-4 w-4">
							<x-slot:right>
								Warnings
							</x-slot:right>
						</x-icon>

					</button>
					<button
							@click="tab = 'history'"
							:class="{'border-b-primary-500 text-primary-500 dark:text-primary-400': tab === 'history'}"
							class="border-b-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="book-open"
								class="h-4 w-4">
							<x-slot:right>
								History
							</x-slot:right>
						</x-icon>

					</button>
					<button
							@click="tab = 'warrants'"
							aria-current="page"
							:class="{'border-b-primary-500 text-primary-500 dark:text-primary-400': tab === 'warrants'}"
							class="border-b-1 px-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="document-magnifying-glass"
								class="h-4 w-4">
							<x-slot:right>
								Warrants
							</x-slot:right>
						</x-icon>
					</button>
					<button
							@click="tab = 'documents'"
							aria-current="page"
							:class="{'border-b-primary-500 text-primary-500 dark:text-primary-400': tab === 'documents'}"
							class="border-b-1 px-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="document-magnifying-glass"
								class="h-4 w-4">
							<x-slot:right>
								Documents
							</x-slot:right>
						</x-icon>
					</button>
					<button
							@click="tab = 'contacts'"
							:class="{'border-b-primary-500 text-primary-500 dark:text-primary-400': tab === 'contacts'}"
							class="border-b-1 px-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="phone"
								class="h-4 w-4">
							<x-slot:right>
								Contacts
							</x-slot:right>
						</x-icon>
					</button>
					<button
							@click="tab = 'assessments'"
							:class="{'border-b-primary-500 text-primary-500 dark:text-primary-400': tab === 'assessments'}"
							class="border-b-1 px-1 py-2 text-sm font-medium whitespace-nowrap text-gray-700 hover:border-gray-200 hover:text-gray-500 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400 hover:cursor-pointer">
						<x-icon
								name="chart-bar"
								class="h-4 w-4">
							<x-slot:right>
								Assessments
							</x-slot:right>
						</x-icon>
					</button>
				</nav>
			</div>
		</div>
	</div>

	<div x-show="tab === 'general'">
		<livewire:pages.negotiation.noc-elements.subject.subject-general
				:primary-subject="$primarySubject"
				:negotiation="$negotiation" />
	</div>

	<div x-show="tab === 'history'">
		<livewire:pages.negotiation.noc-elements.subject.subject-history :subjectId="$primarySubject->id" />
	</div>

	<div x-show="tab === 'warnings'">
		<livewire:pages.negotiation.noc-elements.subject.subject-warnings
				:subjectId="$primarySubject->id"
				:negotiationId="$negotiation->id" />
	</div>
	<div x-show="tab === 'documents'">
		<livewire:pages.negotiation.noc-elements.subject.subject-documents
				:subjectId="$primarySubject->id"
				:negotiationId="$negotiation->id" />
	</div>
	<div
			x-show="tab === 'warrants'"
			class="">
		<livewire:pages.negotiation.noc-elements.subject.warrants
				:subjectId="$primarySubject->id"
				:negotiationId="$negotiation->id" />
	</div>
	<div x-show="tab === 'contacts'">
		<livewire:pages.negotiation.noc-elements.subject.contacts
				:subjectId="$primarySubject->id"
				:negotiationId="$negotiation->id" />
	</div>

	<div x-show="tab === 'assessments'">
		<livewire:pages.negotiation.noc-elements.subject.assessments
				:subjectId="$primarySubject->id"
				:negotiationId="$negotiation->id" />
	</div>

</div>
