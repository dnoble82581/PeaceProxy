<?php

	use Livewire\Attributes\On;
	use Livewire\Volt\Component;
	use App\DTOs\Note\NoteDTO;
	use App\DTOs\Pin\PinDTO;
	use App\Services\Note\NoteCreationService;
	use App\Services\Note\NoteDeletionService;
	use App\Services\Note\NoteFetchingService;
	use App\Services\Note\NoteUpdateService;
	use App\Services\Pin\PinCreationService;
	use App\Services\Pin\PinDeletionService;
	use App\Services\Pin\PinFetchingService;
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
		public $pinnedNotes = [];
		public int $tenantId;

		public function mount($negotiationId = null)
		{
			$this->negotiationId = $negotiationId ?: 0;
			$this->tenantId = auth()->check() ? (int) auth()->user()->tenant_id : 0;
			$this->loadNotes(); // This now also loads pinned notes
		}

		public function loadNotes():void
		{
			$this->notes = app(NoteFetchingService::class)->getNotes();
			$this->loadPinnedNotes();
		}

		public function loadPinnedNotes():void
		{
			$this->pinnedNotes = [];
			$pins = app(PinFetchingService::class)->getPins();

			foreach ($pins as $pin) {
				if ($pin->pinnable_type === 'App\\Models\\Note') {
					$this->pinnedNotes[$pin->pinnable_id] = true;
				}
			}
		}

		public function openCreateModal()
		{
			$this->reset('title', 'body');
			$this->showCreateModal = true;
		}

		public function createNote()
		{
			if (!auth()->check() || empty($this->negotiationId)) {
				return;
			}
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
				$this->body,
				false, // is_private
				false  // pinned - We're no longer using the pinned flag on the Note model
			);

			app(NoteCreationService::class)->createNote($noteDTO);

			$this->reset('title', 'body');
			$this->showCreateModal = false;
			$this->loadNotes();
		}

		public function openEditModal($noteId)
		{
			$note = app(NoteFetchingService::class)->getNote($noteId);
			if (!$note) {
				return;
			}
			$this->editingNoteId = $noteId;
			$this->title = $note->title;
			$this->body = $note->body;
			$this->showEditModal = true;
		}

		public function updateNote()
		{
			if (!$this->editingNoteId) {
				return;
			}
			$this->validate([
				'title' => 'required|string|max:255',
				'body' => 'required|string',
			]);

			$note = app(NoteFetchingService::class)->getNote($this->editingNoteId);

			if (!$note) {
				return;
			}
			$noteDTO = new NoteDTO(
				$this->editingNoteId,
				$note->negotiation_id,
				$note->tenant_id,
				$note->author_id,
				$this->title,
				$this->body,
				$note->is_private,
				false, // We're no longer using the pinned flag on the Note model
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

		public function getListeners()
		{
			if (empty($this->tenantId) || empty($this->negotiationId)) {
				return [];
			}
			return [
				"echo-private:tenants.$this->tenantId.notifications,.NotePinned" => 'loadNotes',
				"echo-private:tenants.$this->tenantId.notifications,.NoteUnpinned" => 'loadNotes',
				"echo-presence:negotiation.$this->negotiationId,.NoteCreated" => 'handleNoteCreated',
			];
		}

		public function handleNoteCreated(array $data)
		{
			$this->loadNotes();
		}

		public function pinNote($noteId):void
		{
			if (!auth()->check()) {
				return;
			}
			$note = app(NoteFetchingService::class)->getNote($noteId);

			if ($note) {
				$pinDTO = new PinDTO(
					null,
					auth()->user()->tenant_id,
					auth()->id(),
					'App\\Models\\Note',
					$noteId
				);

				app(PinCreationService::class)->createPin($pinDTO);
				$tenantId = $this->tenantId ?? (auth()->check() ? auth()->user()->tenant_id : null);
				if ($tenantId) {
					event(new \App\Events\Pin\NotePinnedEvent($tenantId, $noteId));
				}
				$this->loadPinnedNotes();
			}
		}

		public function unpinNote($noteId):void
		{
			app(PinDeletionService::class)->deletePinByPinnable('App\\Models\\Note', $noteId);
			$this->loadPinnedNotes();
		}

		public function isPinned($noteId):bool
		{
			return isset($this->pinnedNotes[$noteId]);
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
				wire:click="openCreateModal"
				sm>Add Note
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
								@if($this->isPinned($note->id))
									<x-button
											color="amber"
											flat="true"
											icon="star"
											wire:click="unpinNote({{ $note->id }})"
											title="Unpin note" />
								@else
									<x-button
											flat="true"
											icon="star"
											wire:click="pinNote({{ $note->id }})"
											title="Pin note" />
								@endif
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
		<div class="text-center">
			<tr>
				<td
						colspan="5"
						class="px-4 py-4 text-center text-xs text-gray-500 dark:text-dark-400">No
				                                                                               objectives
				                                                                               yet.
				</td>
			</tr>
		</div>
	@endif

	<!-- Create Note Modal -->
	<template x-teleport="body">
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
	</template>

	<!-- Edit Note Modal -->
	<template x-teleport="body">
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
	</template>
</div>
