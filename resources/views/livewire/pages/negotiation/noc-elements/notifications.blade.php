<?php

	use Livewire\Volt\Component;
	use App\Services\Pin\PinFetchingService;
	use App\Services\Pin\PinDeletionService;
	use App\Models\Note;

	new class extends Component {
		public $pinnedNotes = [];
		public $showNoteModal = false;
		public $selectedNote = null;
		public int $tenantId;

		public function mount()
		{
			$this->loadPinnedNotes();
			$this->tenantId = tenant()->id;
		}

		public function loadPinnedNotes()
		{
			$this->pinnedNotes = [];
			$pins = app(PinFetchingService::class)->getPins();

			foreach ($pins as $pin) {
				if ($pin->pinnable_type === 'App\\Models\\Note') {
					// Eager load the note with its relationships
					$note = Note::with('author')->find($pin->pinnable_id);
					if ($note) {
						$this->pinnedNotes[] = $note;
					}
				}
			}
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

		public function getListeners()
		{
			return [
				"echo-private:tenants.$this->tenantId.notifications,.NotePinned" => 'loadPinnedNotes',
				"echo-private:tenants.$this->tenantId.notifications,.NoteUnpinned" => 'loadPinnedNotes',
			];
		}
	}

?>

<div
		class="relative"
		x-data="{ open: true }">
	<!-- Toggle button - always positioned on the right -->
	<div class="flex justify-end mb-2">
		<x-button
				color=""
				flat
				@click="open = !open"
				sm
				primary />
	</div>

	<!-- Notifications container -->
	<div
			class="p-2 mt-2 bg-dark-800 rounded-md"
			x-show="open"
			x-transition>
		<h2 class="text-xl font-semibold mb-4">Notifications</h2>

		@if(count($this->pinnedNotes) > 0)
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-4 md:grid-cols-6">
				@foreach($this->pinnedNotes as $note)
					<div class="bg-dark-700 rounded-md p-3 flex items-start group">
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
								wire:click="showNote({{ $note->id }})">
							<h3 class="font-medium dark:text-dark-100 text-dark-800">{{ $note->title }}</h3>
							<p class="text-xs text-dark-300">Pinned
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
		@else
			<div class="text-center py-4 text-gray-500">
				No pinned notes found.
			</div>
		@endif
	</div>

	<!-- Note Modal -->
	<x-modal wire="showNoteModal">
		@if($selectedNote)
			<x-card header="{{ $selectedNote->title }}">
				<div class="p-4">
					<div class="mb-4">
						<p class="text-sm text-dark-800 dark:text-dark-100">By {{ $selectedNote->author->name }}
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
</div>