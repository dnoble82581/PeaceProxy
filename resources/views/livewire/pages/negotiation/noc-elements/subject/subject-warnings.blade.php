<?php

	use App\Factories\MessageFactory;
	use App\Models\Subject;
	use App\Models\User;
	use App\Models\Warning;
	use App\Enums\General\RiskLevels;
	use App\Services\Subject\SubjectFetchingService;
	use App\Services\Warning\WarningDeletionService;
	use App\Services\Warning\WarningFetchingService;
	use Carbon\Carbon;
	use Livewire\Volt\Component;
	use TallStackUi\Traits\Interactions;

	new class extends Component {
		public Subject $subject;
		public int $negotiationId;
		public bool $showCreateWarningModal = false;
		public bool $showEditWarningModal = false;
		public bool $showViewWarningModal = false;
		public int $warningToEditId;
		public ?int $warningToViewId = null;

		use Interactions

		public function mount($subjectId, $negotiationId)
		{
			$this->subject = $this->fetchSubject($subjectId);
			$this->negotiationId = $negotiationId;
		}

		private function fetchSubject($subjectId)
		{
			return app(SubjectFetchingService::class)->fetchSubjectById($subjectId);
		}

		#[\Livewire\Attributes\On('close-modals')]
		public function closeCreateModal()
		{
			$this->showCreateWarningModal = false;
			$this->showEditWarningModal = false;
		}

		#[\Livewire\Attributes\On('close-edit-warning-modal')]
		public function closeEditModal()
		{
			$this->showEditWarningModal = false;
		}

		public function getWarningsProperty()
		{
			return app(WarningFetchingService::class)->fetchWarningsBySubject($this->subject);
		}

		public function deleteWarning($warningId):void
		{
			app(WarningDeletionService::class)->deleteWarning($warningId);
		}

		public function editWarning(int $warningId)
		{
			$this->warningToEditId = $warningId;
			$this->showEditWarningModal = true;
		}

		public function viewWarning(int $warningId):void
		{
			$this->warningToViewId = $warningId;
			$this->showViewWarningModal = true;
		}

		public function getWarningToViewProperty():?Warning
		{
			if (!$this->warningToViewId) {
				return null;
			}
			return Warning::with('createdBy')->find($this->warningToViewId);
		}

		public function getUserName($userId):string
		{
			$user = User::find($userId);
			return $user? $user->name : 'Unknown User';
		}

		public function getTimeAgo($createdAt):string
		{
			return Carbon::parse($createdAt)->diffForHumans();
		}


		public function handleWarningUpdated(array $event)
		{
			$this->dispatch('refresh');

			$warning = app(WarningFetchingService::class)->fetchWarningById($event['warningId']);

			if (!$warning) {
				return; // Exit if the warning is not found
			}

			// Generate the dynamic message
			$messageFactory = app(MessageFactory::class);
			$message = $messageFactory->generateMessage($warning, 'WarningUpdated');

			// Display the toast notification
			$this->toast()->timeout()->info($message)->send();
		}

		public function handleWarningCreated(array $event)
		{
			$this->dispatch('refresh');

			$warning = app(WarningFetchingService::class)->fetchWarningById($event['warningId']);

			if (!$warning) {
				return; // Exit if the warning is not found
			}

			// Generate the dynamic message
			$messageFactory = app(MessageFactory::class);
			$message = $messageFactory->generateMessage($warning, 'WarningCreated');

			// Display the toast notification
			$this->toast()->timeout()->info($message)->send();
		}

		public function handleWarningDeleted(array $event)
		{
			$details = $event['details'] ?? null;
			if ($details) {
				// Create message dynamically with available details
				$message = "{$details['createdBy']} deleted a {$details['label']} warning for {$details['subjectName']}.";
			} else {
				$message = "A warning has been deleted.";
			}

			// Show notification
			$this->toast()->timeout()->info($message)->send();

			// Optionally refresh the data (e.g., warning list)
			$this->dispatch('refresh');
		}

		public function getListeners()
		{
			$subjectId = $this->subject->id;
			return [
				'echo-private:'.\App\Support\Channels\Subject::subjectWarning($subjectId).',.'.\App\Support\EventNames\SubjectEventNames::WARNING_CREATED => 'handleWarningCreated',
				'echo-private:'.\App\Support\Channels\Subject::subjectWarning($subjectId).',.'.\App\Support\EventNames\SubjectEventNames::WARNING_DELETED => 'handleWarningDeleted',
				'echo-private:'.\App\Support\Channels\Subject::subjectWarning($subjectId).',.'.\App\Support\EventNames\SubjectEventNames::WARNING_UPDATED => 'handleWarningUpdated',
			];
		}
	}

?>
<div>
	<div class="text-right px-4 pt-1">
		<x-button.circle
				wire:click="$toggle('showCreateWarningModal')"
				color=""
				flat
				sm
				icon="plus"
		/>
	</div>
	<ul
			role="list"
			class="grid grid-cols-1 gap-3 sm:grid-cols-4 sm:gap-2 lg:grid-cols-4">
		@forelse($this->warnings as $warning)
			<li
					class="col-span-1 flex rounded-md shadow-xs dark:shadow-none overflow-visible"
					wire:key="warning-{{ $warning->id }}">
				<div
						class="flex w-16 shrink-0 items-center justify-center rounded-l-md {{ $warning->risk_level->color() }}  text-sm font-medium text-white"
				>
					<span class="text-xs">{{ $warning->risk_level->label() }}</span>
				</div>
				<div class="flex flex-1 items-center justify-between truncate overflow-visible rounded-r-md border-t border-r border-b border-gray-200 bg-white dark:border-white/10 dark:bg-dark-800/50">
					<div class="flex-1 truncate px-4 py-2 text-sm">
						<span
								class="font-medium text-gray-900 hover:text-gray-600 dark:text-white dark:hover:text-gray-200">{{ $warning->warning_type->label() }}</span>
						<p class="text-gray-500 dark:text-gray-400 text-xs">{{ $warning->createdBy->name }}</p>
					</div>
					<div class="shrink-0 pr-2">
						<x-dropdown
								icon="ellipsis-vertical"
								position="left"
								static>
							<x-dropdown.items
									icon="pencil-square"
									wire:click="editWarning({{ $warning->id }})"
									text="Edit" />
							<x-dropdown.items
									icon="trash"
									wire:click="deleteWarning({{ $warning->id }})"
									text="Delete" />
							<x-dropdown.items
									wire:click="viewWarning({{ $warning->id }})"
									separator
									icon="eye"
									text="View" />
						</x-dropdown>
					</div>
				</div>
			</li>
		@empty
			<div class="col-span-full text-center p-4 text-gray-500">
				No warnings found for this subject.
				<p class="mt-2">
					Click the <span class="inline-flex items-center"><svg
								class="w-4 h-4 text-gray-500"
								fill="none"
								stroke="currentColor"
								viewBox="0 0 24 24"
								xmlns="http://www.w3.org/2000/svg"><path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg></span> button in the top-right corner
					to create a new warning.
				</p>
			</div>
		@endforelse
	</ul>

	<x-modal
			id="CreateWarningModal"
			wire="showCreateWarningModal"
			center>
		<livewire:forms.warning.create-warning
				:subjectId="$subject->id"
				:negotiationId="$negotiationId" />
	</x-modal>
	@if($warningToEditId)
		<x-modal
				id="EditWarningModal"
				wire="showEditWarningModal"
				center>
			<livewire:forms.warning.edit-warning :warning-id="$warningToEditId" />
		</x-modal>
	@endif
	<x-modal
			id="viewWarningModal"
			wire="showViewWarningModal"
			center>
		@if($this->warningToView)
			<div class="space-y-4">
				<div class="flex items-start justify-between">
					<div>
						<h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
							{{ $this->warningToView->warning_type->label() }} Warning
						</h3>
						<p class="text-xs text-gray-500 dark:text-gray-400">
							Created {{ $this->warningToView->created_at?->diffForHumans() }}
							by {{ $this->warningToView->createdBy->name ?? 'Unknown' }}
						</p>
					</div>
					<span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium text-white {{ $this->warningToView->risk_level->color() }}">
						{{ $this->warningToView->risk_level->label() }} Risk
					</span>
				</div>

				<div class="rounded-md bg-gray-50 dark:bg-dark-700 p-3 border border-gray-200 dark:border-white/10">
					<h4 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1">Warning</h4>
					<p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
						{{ $this->warningToView->warning }}
					</p>
				</div>

				<div class="grid grid-cols-2 gap-3 text-xs text-gray-600 dark:text-gray-400">
					<div>
						<span class="font-semibold">Type:</span>
						{{ $this->warningToView->warning_type->label() }}
					</div>
					<div class="text-right">
						<span class="font-semibold">Created:</span>
						{{ $this->warningToView->created_at?->setTimezone(auth()->user()->timezone)->format('M d, Y H:i') }}
					</div>
				</div>
			</div>
		@else
			<div class="text-sm text-gray-600 dark:text-gray-300">No warning selected.</div>
		@endif
	</x-modal>
</div>

