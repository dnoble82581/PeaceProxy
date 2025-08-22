<?php

	use Livewire\Attributes\On;
	use Livewire\Volt\Component;
	use App\DTOs\Note\NoteDTO;
	use App\Services\Note\NoteCreationService;
	use App\Services\Note\NoteDeletionService;
	use App\Services\Note\NoteFetchingService;
	use App\Services\Note\NoteUpdateService;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Auth;

	new class extends Component {
		public $notes = [];
		public $showCreateModal = false;
		public $showEditModal = false;
		public $title = '';
		public $body = '';
		public $editingNoteId = null;
		public $negotiationId = null;

		public function mount($negotiationId = null)
		{
			$this->negotiationId = $negotiationId;
			$this->loadNotes();
		}

		public function loadNotes():void
		{
			$this->notes = app(NoteFetchingService::class)->getNotes();
		}

		public function openCreateModal()
		{
			$this->reset('title', 'body');
			$this->showCreateModal = true;
		}

		public function createNote()
		{
			$this->validate([
				'title' => 'required|string|max:255',
				'body' => 'required|string',
			]);

			$noteDTO = new NoteDTO(
				null,
				$this->negotiationId,
				auth()->user()->tenant_id,
				auth()->id(),
				$this->title,
				$this->body
			);

			app(NoteCreationService::class)->createNote($noteDTO);

			$this->reset('title', 'body');
			$this->showCreateModal = false;
			$this->loadNotes();
		}

		public function openEditModal($noteId)
		{
			$note = app(NoteFetchingService::class)->getNote($noteId);
			$this->editingNoteId = $noteId;
			$this->title = $note->title;
			$this->body = $note->body;
			$this->showEditModal = true;
		}

		public function updateNote()
		{
			$this->validate([
				'title' => 'required|string|max:255',
				'body' => 'required|string',
			]);

			$note = app(NoteFetchingService::class)->getNote($this->editingNoteId);

			$noteDTO = new NoteDTO(
				$this->editingNoteId,
				$note->negotiation_id,
				$note->tenant_id,
				$note->author_id,
				$this->title,
				$this->body,
				$note->is_private,
				$note->pinned,
				$note->tags,
				$note->created_at,
				Carbon::now()
			);

			app(NoteUpdateService::class)->updateNote($noteDTO, $this->editingNoteId);

			$this->reset('title', 'body', 'editingNoteId');
			$this->showEditModal = false;
			$this->loadNotes();
		}

		/**
		 * Close all modal dialogs
		 *
		 * This method is triggered by the 'close-modal' event
		 *
		 * @return void
		 */
		#[On('close-modal')]
		public function closeModal():void
		{
			$this->showCreateModal = false;
			$this->showEditModal = false;
		}

		public function deleteNote($noteId):void
		{
			app(NoteDeletionService::class)->deleteNote($noteId);
			$this->loadNotes();
		}
	}

?>

<div
		class="relative"
		x-data="{ minimize: false }">
	<div class="mb-4 flex justify-between items-center">
		<h2 class="text-xl font-semibold">Notes</h2>
		<x-button
				icon="plus"
				wire:click="openCreateModal">Add Note
		</x-button>
	</div>

	@if($notes && count($notes))
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
			@foreach($notes as $note)
				<x-card>
					<x-slot:header><h3 class="p-2">{{ $note->title }}</h3></x-slot:header>
					<p>{{ $note->body }}</p>
					<x-slot:footer>
						<div class="flex justify-between items-center">
							<div>
								<p class="text-xs">Created
								                   by {{ $note->author->name }} {{ $note->created_at->diffForHumans() }}</p>
							</div>
							<div>
								<x-button
										flat="true"
										icon="pencil-square"
										wire:click="openEditModal({{ $note->id }})" />
								<x-button
										color="rose"
										flat="true"
										icon="trash"
										wire:click="deleteNote({{ $note->id }})" />
							</div>
						</div>
					</x-slot:footer>
				</x-card>
			@endforeach
		</div>
	@else
		<div class="text-center py-4">
			<p class="text-gray-500">No notes found. Click "Add Note" to create one.</p>
		</div>
	@endif

	<!-- Create Note Modal -->
	<x-modal
			id="create-note-modal"
			wire="showCreateModal"
			x-on:hidden.window="$wire.closeModal()">
		<x-card title="Create New Note">
			<div class="space-y-4">
				<x-input
						label="Title"
						wire:model="title" />
				<x-textarea
						label="Content"
						wire:model="body"
						rows="5" />
			</div>

			<x-slot:footer>
				<div class="flex justify-end gap-x-2">
					<x-button
							flat
							wire:click="$set('showCreateModal', false)">Cancel
					</x-button>
					<x-button
							primary
							wire:click="createNote">Save
					</x-button>
				</div>
			</x-slot:footer>
		</x-card>
	</x-modal>

	<!-- Edit Note Modal -->
	<x-modal
			id="edit-note-modal"
			wire="showEditModal"
			x-on:hidden.window="$wire.closeModal()">
		<x-card title="Edit Note">
			<div class="space-y-4">
				<x-input
						label="Title"
						wire:model="title" />
				<x-textarea
						label="Content"
						wire:model="body"
						rows="5" />
			</div>

			<x-slot:footer>
				<div class="flex justify-end gap-x-2">
					<x-button
							flat
							wire:click="$set('showEditModal', false)">Cancel
					</x-button>
					<x-button
							primary
							wire:click="updateNote">Update
					</x-button>
				</div>
			</x-slot:footer>
		</x-card>
	</x-modal>
</div>
