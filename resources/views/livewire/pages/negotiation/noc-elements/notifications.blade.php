<?php

	use Livewire\Volt\Component;
	use App\Services\Pin\PinFetchingService;
	use App\Services\Pin\PinDeletionService;
	use App\Models\Note;
	use App\Models\Objective;
	use App\Models\Log;

	new class extends Component {
		public $pinnedNotes = [];
		public $pinnedObjectives = [];
		public $activityLogs = [];
		public $showNoteModal = false;
		public $showObjectiveModal = false;
		public $showActivityLogModal = false;
		public $showLogDetailModal = false;
		public $selectedNote = null;
		public $selectedObjective = null;
		public $selectedLog = null;
		public int $tenantId;

		public function mount()
		{
			$this->loadPinnedNotes();
			$this->loadActivityLogs();
			$this->tenantId = tenant()->id;
		}

		public function loadActivityLogs()
		{
			$this->activityLogs = Log::forTenant(tenant()->id)
				->orderBy('occurred_at', 'desc')
				->limit(10)
				->get();
		}

		public function showLog($logId)
		{
			$this->selectedLog = null;

			foreach ($this->activityLogs as $log) {
				if ($log->id === $logId) {
					$this->selectedLog = $log;
					break;
				}
			}

			if ($this->selectedLog) {
				$this->showLogDetailModal = true;
			}
		}

		public $previousPinnedNoteIds = [];
		public $previousPinnedObjectiveIds = [];

		public function loadPinnedNotes()
		{
			$this->pinnedNotes = [];
			$this->pinnedObjectives = [];
			$pins = app(PinFetchingService::class)->getPins();

			$currentPinnedNoteIds = [];
			$currentPinnedObjectiveIds = [];

			foreach ($pins as $pin) {
				if ($pin->pinnable_type === 'App\\Models\\Note') {
					// Eager load the note with its relationships
					$note = Note::with('author')->find($pin->pinnable_id);
					if ($note) {
						$this->pinnedNotes[] = $note;
						$currentPinnedNoteIds[] = $note->id;
					}
				} elseif ($pin->pinnable_type === 'App\\Models\\Objective') {
					// Eager load the objective with its relationships
					$objective = Objective::with(['createdBy', 'completedBy'])->find($pin->pinnable_id);
					if ($objective) {
						$this->pinnedObjectives[] = $objective;
						$currentPinnedObjectiveIds[] = $objective->id;
					}
				}
			}

			// Check for new pinned notes
			foreach ($currentPinnedNoteIds as $noteId) {
				if (!in_array($noteId, $this->previousPinnedNoteIds)) {
					$this->dispatch('notePinned');
					break;
				}
			}

			// Check for new pinned objectives
			foreach ($currentPinnedObjectiveIds as $objectiveId) {
				if (!in_array($objectiveId, $this->previousPinnedObjectiveIds)) {
					$this->dispatch('objectivePinned');
					break;
				}
			}

			// Update previous IDs for next comparison
			$this->previousPinnedNoteIds = $currentPinnedNoteIds;
			$this->previousPinnedObjectiveIds = $currentPinnedObjectiveIds;
		}

		public function showNote($noteId)
		{
			$this->selectedNote = null;

			foreach ($this->pinnedNotes as $note) {
				if ($note->id === $noteId) {
					$this->selectedNote = $note;
					break;
				}
			}

			if ($this->selectedNote) {
				$this->showNoteModal = true;
			}
		}

		public function unpinNote($noteId)
		{
			app(PinDeletionService::class)->deletePinByPinnable('App\\Models\\Note', $noteId);
			event(new \App\Events\Pin\NoteUnpinnedEvent(tenant()->id, $noteId));
			$this->loadPinnedNotes();
		}

		public function showObjective($objectiveId)
		{
			$this->selectedObjective = null;

			foreach ($this->pinnedObjectives as $objective) {
				if ($objective->id === $objectiveId) {
					$this->selectedObjective = $objective;
					break;
				}
			}

			if ($this->selectedObjective) {
				$this->showObjectiveModal = true;
			}
		}

		public function unpinObjective($objectiveId)
		{
			app(PinDeletionService::class)->deletePinByPinnable('App\\Models\\Objective', $objectiveId);
			event(new \App\Events\Pin\ObjectiveUnpinnedEvent(tenant()->id, $objectiveId));
			$this->loadPinnedNotes();
		}

		public function getListeners()
		{
			return [
				"echo-private:tenants.$this->tenantId.notifications,.NotePinned" => 'loadPinnedNotes',
				"echo-private:tenants.$this->tenantId.notifications,.NoteUnpinned" => 'loadPinnedNotes',
				"echo-private:tenants.$this->tenantId.notifications,.ObjectivePinned" => 'loadPinnedNotes',
				"echo-private:tenants.$this->tenantId.notifications,.ObjectiveUnpinned" => 'loadPinnedNotes',
			];
		}
	}

?>

<div
		class="relative mt-2 mb-2"
		x-data="{ open: false, hasNewNotifications: false }"
		x-init="
			$wire.on('notePinned', () => { if (!open) hasNewNotifications = true; });
			$wire.on('objectivePinned', () => { if (!open) hasNewNotifications = true; });
		">
	<!-- Toggle buttons - always positioned on the right -->
	<div class="flex justify-end mb-2 space-x-2">
		<x-button
				color="emerald"
				flat
				wire:click="$toggle('showActivityLogModal')"
				sm
				icon="clock"
				class="relative">
			Activity Logs
		</x-button>

		<x-button
				color="sky"
				flat
				@click="open = !open; if (open) hasNewNotifications = false;"
				sm
				icon="bell"
				class="relative">
			<span x-text="open ? 'Hide' : 'Show'"></span> Notifications
			                                              <!-- Alert indicator for new notifications -->
			<span
					x-show="!open && hasNewNotifications"
					class="absolute -top-1 -right-1 flex h-3 w-3">
				<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
				<span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
			</span>
		</x-button>
	</div>

	<!-- Notifications container -->
	<div
			class="p-2 mb-2 bg-white dark:bg-dark-800 rounded-md shadow"
			x-show="open"
			x-transition>
		{{--		<h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Notifications</h2>--}}

		<!-- Pinned Notes Section -->
		@if(count($this->pinnedNotes) > 0)
			<h3 class="text-lg font-medium mb-1 text-gray-800 dark:text-white">Pinned Notes</h3>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-4 md:grid-cols-5 mb-2">
				@foreach($this->pinnedNotes as $note)
					<div class="bg-gray-100 dark:bg-dark-700 rounded-md p-3 flex items-start group shadow-sm">
						<div class="text-sky-500 mr-2 flex-shrink-0">
							<svg
									xmlns="http://www.w3.org/2000/svg"
									class="h-5 w-5"
									viewBox="0 0 20 20"
									fill="currentColor">
								<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
							</svg>
						</div>
						<div
								class="flex-1 cursor-pointer"
								wire:click="showNote({{ $note->id }})">
							<h3 class="font-medium text-gray-900 dark:text-dark-100">{{ $note->title }}</h3>
							<p class="text-xs text-gray-500 dark:text-dark-300">Pinned
							                                                    by {{ $note->author->name }} {{ $note->created_at->diffForHumans() }}</p>
						</div>
						<div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
							<x-button
									color="rose"
									flat
									icon="x-mark"
									size="xs"
									wire:click="unpinNote({{ $note->id }})"
									title="Unpin note" />
						</div>
					</div>
				@endforeach
			</div>
		@endif

		<!-- Pinned Objectives Section -->
		@if(count($this->pinnedObjectives) > 0)
			<h3 class="text-lg font-medium mb-2 text-gray-800 dark:text-white">Pinned Objectives</h3>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-4 md:grid-cols-5">
				@foreach($this->pinnedObjectives as $objective)
					<div class="bg-gray-100 dark:bg-dark-700 rounded-md p-3 flex items-start group shadow-sm">
						<div class="text-amber-500 mr-2 flex-shrink-0">
							<svg
									xmlns="http://www.w3.org/2000/svg"
									class="h-5 w-5"
									viewBox="0 0 20 20"
									fill="currentColor">
								<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
							</svg>
						</div>
						<div
								class="flex-1 cursor-pointer"
								wire:click="showObjective({{ $objective->id }})">
							<h3 class="font-medium text-gray-900 dark:text-dark-100">{{ Str::limit($objective->objective, 20) }}</h3>
							<p class="text-xs text-gray-500 dark:text-dark-300">
								<span class="inline-flex items-center rounded-md bg-{{ $objective->priority->color() }}-50 px-1.5 py-0.5 text-xs font-medium text-{{ $objective->priority->color() }}-700 ring-1 ring-inset ring-{{ $objective->priority->color() }}-600/20">
									{{ $objective->priority->label() }}
								</span>
								· Pinned
								by {{ $objective->createdBy->name }} {{ $objective->created_at->diffForHumans() }}
							</p>
						</div>
						<div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
							<x-button
									color="rose"
									flat
									icon="x-mark"
									size="xs"
									wire:click="unpinObjective({{ $objective->id }})"
									title="Unpin objective" />
						</div>
					</div>
				@endforeach
			</div>
		@endif

		@if(count($this->pinnedNotes) === 0 && count($this->pinnedObjectives) === 0)
			<div class="text-center py-4 text-gray-500 dark:text-gray-400">
				No pinned items found.
			</div>
		@endif
	</div>

	<!-- Note Modal -->
	<x-modal wire="showNoteModal">
		@if($selectedNote)
			<x-card header="{{ $selectedNote->title }}">
				<div class="p-4">
					<div class="mb-4">
						<p class="text-sm text-gray-700 dark:text-gray-300">By {{ $selectedNote->author->name }}
						                                                    · {{ $selectedNote->created_at->format('M d, Y') }}</p>
					</div>
					<div class="prose dark:prose-invert max-w-none">
						{{ $selectedNote->body }}
					</div>
				</div>

				<x-slot:footer>
					<div class="flex justify-between">
						<x-button
								color="rose"
								wire:click="unpinNote({{ $selectedNote->id }})"
								icon="x-mark">
							Unpin
						</x-button>
						<x-button wire:click="$set('showNoteModal', false)">
							Close
						</x-button>
					</div>
				</x-slot:footer>
			</x-card>
		@endif
	</x-modal>

	<!-- Objective Modal -->
	<x-modal wire="showObjectiveModal">
		@if($selectedObjective)
			<x-card header="Objective Details">
				<div class="p-4">
					<div class="mb-4 flex items-center gap-2">
						<p class="text-sm text-gray-700 dark:text-gray-300">
							Created by {{ $selectedObjective->createdBy->name }}
							· {{ $selectedObjective->created_at->format('M d, Y') }}
						</p>
						<x-badge
								text="{{ $selectedObjective->priority->label() }}"
								color="{{ $selectedObjective->priority->color() }}" />
						<x-badge
								text="{{ $selectedObjective->status->label() }}"
								color="{{ $selectedObjective->status->color() }}" />
					</div>
					<div class="prose dark:prose-invert max-w-none">
						<p class="text-gray-800 dark:text-gray-200">{{ $selectedObjective->objective }}</p>
					</div>

					@if($selectedObjective->status === 'completed' && $selectedObjective->completed_at)
						<div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
							<p class="text-sm text-gray-700 dark:text-gray-300">
								Completed by {{ $selectedObjective->completedBy->name }}
								· {{ $selectedObjective->completed_at->format('M d, Y') }}
							</p>
						</div>
					@endif
				</div>

				<x-slot:footer>
					<div class="flex justify-between">
						<x-button
								color="rose"
								wire:click="unpinObjective({{ $selectedObjective->id }})"
								icon="x-mark">
							Unpin
						</x-button>
						<x-button wire:click="$set('showObjectiveModal', false)">
							Close
						</x-button>
					</div>
				</x-slot:footer>
			</x-card>
		@endif
	</x-modal>

	<!-- Activity Log Modal -->
	<x-modal
			wire="showActivityLogModal"
			size="5xl">
		<x-card header="Recent Activity Logs">
			<div class="p-4">
				@if(count($this->activityLogs) > 0)
					<div class="space-y-4">
						@foreach($this->activityLogs as $log)
							<div
									class="bg-gray-100 dark:bg-dark-700 rounded-md p-3 flex items-start group shadow-sm hover:bg-gray-200 dark:hover:bg-dark-600 cursor-pointer"
									wire:click="showLog({{ $log->id }})">
								<div class="text-emerald-500 mr-2 flex-shrink-0">
									<svg
											xmlns="http://www.w3.org/2000/svg"
											class="h-5 w-5"
											viewBox="0 0 20 20"
											fill="currentColor">
										<path
												fill-rule="evenodd"
												d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
												clip-rule="evenodd" />
									</svg>
								</div>
								<div class="flex-1">
									<h3 class="font-medium text-gray-900 dark:text-dark-100">{{ $log->headline }}</h3>
									<p class="text-xs text-gray-500 dark:text-dark-300">
										{{ $log->event }} · {{ $log->occurred_at->diffForHumans() }}
									</p>
								</div>
							</div>
						@endforeach
					</div>
				@else
					<div class="text-center py-4 text-gray-500 dark:text-gray-400">
						No activity logs found.
					</div>
				@endif
			</div>

			<x-slot:footer>
				<div class="flex justify-end">
					<x-button wire:click="$set('showActivityLogModal', false)">
						Close
					</x-button>
				</div>
			</x-slot:footer>
		</x-card>
	</x-modal>

	<!-- Activity Log Detail Modal -->
	<x-modal
			wire="showLogDetailModal"
			x-data="{ open: false }"
			x-show="open"
			x-on:showactivitylogmodal.window="open = false"
			x-on:closemodal.window="open = false">
		@if($selectedLog)
			<x-card header="Activity Log Details">
				<div class="p-4">
					<div class="mb-4">
						<h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $selectedLog->headline }}</h3>
						<p class="text-sm text-gray-700 dark:text-gray-300">
							{{ $selectedLog->occurred_at->format('M d, Y g:i A') }}
						</p>
					</div>

					@if($selectedLog->description)
						<div class="mb-4">
							<h4 class="font-medium text-gray-800 dark:text-gray-200">Description</h4>
       <p class="text-gray-700 dark:text-gray-300">{{ $selectedLog->description }}</p>
						</div>
					@endif

					<div class="mb-4">
						<h4 class="font-medium text-gray-800 dark:text-gray-200">Event</h4>
						<p class="text-gray-700 dark:text-gray-300">{{ $selectedLog->event }}</p>
					</div>

					@if($selectedLog->properties && count($selectedLog->properties) > 0)
						<div class="mb-4">
							<h4 class="font-medium text-gray-800 dark:text-gray-200">Properties</h4>
							<div class="bg-gray-50 dark:bg-dark-800 p-2 rounded">
								<pre class="text-xs text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ json_encode($selectedLog->properties, JSON_PRETTY_PRINT) }}</pre>
							</div>
						</div>
					@endif
				</div>

				<x-slot:footer>
					<div class="flex justify-end">
						<x-button wire:click="$set('showLogDetailModal', false)">
							Close
						</x-button>
					</div>
				</x-slot:footer>
			</x-card>
		@endif
	</x-modal>
</div>