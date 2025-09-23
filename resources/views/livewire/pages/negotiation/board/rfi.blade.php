<?php

	use Illuminate\Contracts\View\View;
	use Illuminate\Database\Eloquent\Builder;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;
	use App\DTOs\RequestForInformation\RequestForInformationDTO;
	use App\DTOs\RequestForInformationReply\RequestForInformationReplyDTO;
	use App\Services\RequestForInformation\RequestForInformationCreationService;
	use App\Services\RequestForInformation\RequestForInformationDestructionService;
	use App\Services\RequestForInformation\RequestForInformationFetchingService;
	use App\Services\RequestForInformation\RequestForInformationUpdateService;
	use App\Services\RequestForInformationReply\RequestForInformationReplyCreationService;
	use App\Services\RequestForInformationReply\RequestForInformationReplyFetchingService;
	use App\Services\RequestForInformationRecipient\RequestForInformationRecipientCreationService;
	use App\Services\RequestForInformationRecipient\RequestForInformationRecipientFetchingService;
	use App\Services\RequestForInformationRecipient\RequestForInformationRecipientUpdateService;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Auth;
	use Livewire\WithPagination;
	use Livewire\WithFileUploads;

	new class extends Component {
		use WithPagination;
		use WithFileUploads;

		public $rfis = [];
		// Holds the current table rows so internal events can update counts without errors
		public array $rows = [];
		public $showCreateModal = false;
		public $showEditModal = false;
		public $showResponsesModal = false;
		public $title = '';
		public $body = '';
		public $status = 'Pending';
		public $recipients = [];
		public $availableUsers = [];
		public $selectedRecipients = [];
		public $editingRfiId = null;
		public $viewingRfiId = null;
		public $viewingRfi = null;
		public $negotiationId = null;
		public $replies = [];
		public $replyBody = '';

		// Document attachments state for RFI
		public $docFile = null;
		public string $docName = '';
		public ?string $docCategory = null;
		public ?string $docDescription = null;
		public bool $docPrivate = false;
		public bool $showUploadDocModal = false;
		public bool $showViewDocModal = false;
		public $currentDocument = null;
		public $documentUrl = null;
		public int $tenantId;
		public ?int $quantity = 10;     // per page
		public ?string $search = null;  // search box

		protected array $queryString = [
			'search' => ['except' => null],
			'quantity' => ['except' => 10],
			'sort' => ['except' => ['column' => 'created_at', 'direction' => 'desc']],
			'page' => ['except' => 1],
		];

		public array $sort = [
			'column' => 'created_at',
			'direction' => 'desc',
		];


		public function mount($negotiationId = null)
		{
			$this->negotiationId = $negotiationId;
			$this->tenantId = tenant()->id;
			$this->loadRfis();
			$this->loadAvailableUsers();

		}


		public function with():array
		{
			$authUserId = auth()->id();

			$headers = [
				['index' => 'id', 'label' => '#'],
				['index' => 'title', 'label' => 'Title'],
				['index' => 'status', 'label' => 'Status'],
				['index' => 'unread_replies_count', 'label' => 'Unread'],
				['index' => 'show_alert', 'label' => 'Alert'],
				['index' => 'created_at', 'label' => 'Created'],
				['index' => 'replies_count', 'label' => 'Replies'],
				// You can add an 'action' column and fill it via @interact if you want buttons
				['index' => 'action', 'label' => ''],
			];

			// Base query with eager counts
			$query = \App\Models\RequestForInformation::query()
				->withCount('replies')
				->when($this->search, function (Builder $q) {
					$term = '%'.trim($this->search).'%';
					// adjust fields as needed to match your rfiMatchesSearch() logic
					$q->where(function (Builder $sub) use ($term) {
						$sub->where('title', 'like', $term)
							->orWhere('status', 'like', $term);
					});
				});

			// Sorting from TallStackUI $sort prop
			if (!empty($this->sort['column']) && !empty($this->sort['direction'])) {
				$query->orderBy($this->sort['column'], $this->sort['direction']);
			}

			// Return a paginator and transform each item to the shape your table expects.
			// The `through()` keeps pagination metadata intact.
			$rows = $query
				->paginate(max(1, (int) $this->quantity))
				->withQueryString()
				->through(function (\App\Models\RequestForInformation $rfi) use ($authUserId) {
					$repliesCount = (int) ($rfi->replies_count ?? $rfi->replies()->count());

					$recipient = app(RequestForInformationRecipientFetchingService::class)
						->getRecipientByRfiIdAndUserId($rfi->id, $authUserId);

					$unreadRepliesCount = 0;
					$showAlert = false;

					if ($recipient && !$recipient->is_read) {
						$unreadRepliesCount = $repliesCount;
						$showAlert = true;
					} elseif ($recipient) {
						$unreadRepliesCount = (int) $rfi->replies()->where('is_read', false)->count();
						$showAlert = $unreadRepliesCount > 0;
					}

					return [
						'id' => $rfi->id,
						'title' => $rfi->title,
						'status' => $rfi->status,
						'replies_count' => $repliesCount,
						'unread_replies_count' => $unreadRepliesCount,
						'show_alert' => $showAlert,
						'created_at' => $rfi->created_at->format('Y-m-d H:i'),
					];
				});

//			dd($rows->items(), $headers);

			// Keep a plain array of rows on the component for internal updates (e.g., marking unread counts)
			$this->rows = method_exists($rows, 'items')? $rows->items() : (is_array($rows)? $rows : []);
			return compact('headers', 'rows');
		}

		public function loadRfis()
		{
			if ($this->negotiationId) {
				$this->rfis = app(RequestForInformationFetchingService::class)->getRfisByNegotiationId($this->negotiationId);
			} else {
				$this->rfis = app(RequestForInformationFetchingService::class)->getAllRfis();
			}
		}

		public function updatedSearch()
		{
			$this->resetPage();
		}

		public function updatedPerPage()
		{
			// Ensure perPage is always a positive integer
			if (empty($this->perPage) || intval($this->perPage) <= 0) {
				$this->perPage = 10; // Default to 10 if invalid value
			}

			$this->resetPage();
		}

		private function rfiMatchesSearch($rfi, $searchTerm)
		{
			// Convert search term to lowercase for case-insensitive comparison
			$searchTerm = strtolower($searchTerm);

			// Check if search term is in title, status, or ID
			if (str_contains(strtolower($rfi->title), $searchTerm) ||
				str_contains(strtolower($rfi->status), $searchTerm) ||
				str_contains((string) $rfi->id, $searchTerm) ||
				str_contains(strtolower($rfi->body), $searchTerm)) {
				return true;
			}

			// Check if search term is in sender's name
			if (str_contains(strtolower($rfi->sender->name), $searchTerm)) {
				return true;
			}

			return false;
		}

		public function sort($field)
		{
			if ($this->sortField === $field) {
				$this->sortDirection = $this->sortDirection === 'asc'? 'desc' : 'asc';
			} else {
				$this->sortField = $field;
				$this->sortDirection = 'asc';
			}

			$this->prepareRows();
		}

		public function loadAvailableUsers()
		{
			// Load all users from the tenant except the current authenticated user
			$users = \App\Models\User::where('tenant_id', $this->tenantId)
				->where('id', '!=', auth()->id())
				->get();

			// If we have a negotiation ID, load the negotiation roles for each user
			if ($this->negotiationId) {
				foreach ($users as $user) {
					// Get the negotiation_user pivot record for this user and negotiation
					$negotiationUser = \App\Models\NegotiationUser::where('user_id', $user->id)
						->where('negotiation_id', $this->negotiationId)
						->first();

					// If the user has a role in this negotiation, add it to the user object
					if ($negotiationUser && $negotiationUser->role) {
						$user->negotiation_role = $negotiationUser->role->label();
					} else {
						$user->negotiation_role = null;
					}
				}
			}

			$this->availableUsers = $users;
		}

		public function openCreateModal()
		{
			$this->reset('title', 'body', 'status', 'selectedRecipients');
			$this->status = 'Pending';
			$this->showCreateModal = true;
		}

		public function createRfi()
		{
			$this->validate([
				'title' => 'required|string|max:255',
				'body' => 'required|string',
				'status' => 'required|string',
				'selectedRecipients' => 'required|array|min:1',
			]);

			// Ensure the current user is not in the selected recipients
			if (in_array(auth()->id(), $this->selectedRecipients)) {
				$this->addError('selectedRecipients', 'You cannot send a request for information to yourself.');
				return;
			}

			$rfiDTO = new RequestForInformationDTO(
				null,
				$this->tenantId,
				$this->negotiationId,
				auth()->id(),
				$this->title,
				$this->body,
				$this->status,
				null, // due_date
				Carbon::now(),
				Carbon::now(),
				null // deleted_at
			);

			$rfi = app(RequestForInformationCreationService::class)->createRfi($rfiDTO);

			// Create recipients
			foreach ($this->selectedRecipients as $recipientId) {
				$recipientDTO = new \App\DTOs\RequestForInformationRecipient\RequestForInformationRecipientDTO(
					null,
					$this->tenantId,
					$rfi->id,
					$recipientId,
					false, // is_read
					null, // read_at
					Carbon::now(),
					Carbon::now(),
					null // deleted_at
				);

				app(RequestForInformationRecipientCreationService::class)->createRecipient($recipientDTO, $rfi);
			}

			// Optionally attach a document during creation
			if ($this->docFile) {
				$this->validate([
					'docFile' => 'file|max:10240',
					'docName' => 'required|string|max:255',
					'docCategory' => 'nullable|string|max:255',
					'docDescription' => 'nullable|string',
					'docPrivate' => 'boolean',
				]);

				$data = [
					'name' => $this->docName ?: ($this->title.' Attachment'),
					'category' => $this->docCategory,
					'description' => $this->docDescription,
					'is_private' => (bool) $this->docPrivate,
					'uploaded_by_id' => auth()->id(),
					'negotiation_id' => $this->negotiationId,
					'documentable_type' => 'App\\Models\\RequestForInformation',
					'documentable_id' => $rfi->id,
					'tenant_id' => $this->tenantId,
				];

				app(\App\Services\Document\DocumentStorageService::class)->createRfiDocument($data, $rfi->id, $this->docFile);
			}

			$this->reset('title', 'body', 'status', 'selectedRecipients');
			$this->resetDocForm();
			$this->showCreateModal = false;
			$this->loadRfis();
		}

		public function openEditModal($rfiId)
		{
			$rfi = app(RequestForInformationFetchingService::class)->getRfiById($rfiId);
			$this->editingRfiId = $rfiId;
			$this->title = $rfi->title;
			$this->body = $rfi->body;
			$this->status = $rfi->status;

			// Load current recipients
			$recipients = app(RequestForInformationRecipientFetchingService::class)->getRecipientsByRfiId($rfiId);
			$this->selectedRecipients = $recipients->pluck('user_id')->toArray();

			$this->showEditModal = true;
		}

		public function updateRfi()
		{
			$this->validate([
				'title' => 'required|string|max:255',
				'body' => 'required|string',
				'status' => 'required|string',
				'selectedRecipients' => 'required|array|min:1',
			]);

			// Ensure the current user is not in the selected recipients
			if (in_array(auth()->id(), $this->selectedRecipients)) {
				$this->addError('selectedRecipients', 'You cannot send a request for information to yourself.');
				return;
			}

			$rfi = app(RequestForInformationFetchingService::class)->getRfiById($this->editingRfiId);

			$rfiDTO = new RequestForInformationDTO(
				$this->editingRfiId,
				$rfi->tenant_id,
				$rfi->negotiation_id,
				$rfi->user_id,
				$this->title,
				$this->body,
				$this->status,
				null, // due_date
				$rfi->created_at,
				Carbon::now(),
				null // deleted_at
			);

			app(RequestForInformationUpdateService::class)->updateRfi($this->editingRfiId, $rfiDTO);

			// Update recipients - first get current recipients
			$currentRecipients = app(RequestForInformationRecipientFetchingService::class)->getRecipientsByRfiId($this->editingRfiId);
			$currentRecipientIds = $currentRecipients->pluck('user_id')->toArray();

			// Add new recipients
			foreach ($this->selectedRecipients as $recipientId) {
				if (!in_array($recipientId, $currentRecipientIds)) {
					$recipientDTO = new \App\DTOs\RequestForInformationRecipient\RequestForInformationRecipientDTO(
						null,
						$this->tenantId,
						$this->editingRfiId,
						$recipientId,
						false, // is_read
						null, // read_at
						Carbon::now(),
						Carbon::now(),
						null // deleted_at
					);

					app(RequestForInformationRecipientCreationService::class)->createRecipient($recipientDTO);
				}
			}

			// Remove recipients that are no longer selected
			foreach ($currentRecipients as $recipient) {
				if (!in_array($recipient->user_id, $this->selectedRecipients)) {
					app(\App\Services\RequestForInformationRecipient\RequestForInformationRecipientDestructionService::class)->deleteRecipient($recipient->id);
				}
			}

			$this->reset('title', 'body', 'status', 'selectedRecipients', 'editingRfiId');
			$this->showEditModal = false;
			$this->loadRfis();
		}

		public function openResponsesModal($rfiId)
		{
			$this->viewingRfiId = $rfiId;

			// Load the RFI details
			$this->viewingRfi = app(RequestForInformationFetchingService::class)->getRfiById($rfiId);
			if ($this->viewingRfi) {
				$this->viewingRfi->load('documents');
			}

			$this->loadReplies();
			$this->showResponsesModal = true;

			// Mark as read if current user is a recipient
			$recipient = app(RequestForInformationRecipientFetchingService::class)
				->getRecipientByRfiIdAndUserId($rfiId, auth()->id());

			if ($recipient && !$recipient->is_read) {
				app(RequestForInformationRecipientUpdateService::class)->updateReadStatus($recipient->id, true);
			}

			// Mark all replies as read
			if ($recipient) {
				// Get all unread replies for this RFI
				$unreadReplies = $this->viewingRfi->replies()->where('is_read', false)->get();

				// Mark each reply as read
				foreach ($unreadReplies as $reply) {
					$reply->is_read = true;
					$reply->save();
				}

//				ToDO:Fix this piece in the table
				// Update the row data to reflect that all replies are now read
				foreach ($this->rows as &$row) {
					if ($row['id'] === $rfiId) {
						$row['unread_replies_count'] = 0;
						$row['show_alert'] = false;
						break;
					}
				}
			}
		}

		public function loadReplies()
		{
			if ($this->viewingRfiId) {
				$this->replies = app(RequestForInformationReplyFetchingService::class)
					->getRepliesByRfiId($this->viewingRfiId);
				$this->replyBody = '';
			}
		}

		public function submitReply()
		{
			$this->validate([
				'replyBody' => 'required|string',
			]);

			$replyDTO = new RequestForInformationReplyDTO(
				null,
				$this->tenantId,
				$this->viewingRfiId,
				auth()->id(),
				$this->replyBody,
				false,
				Carbon::now(),
				Carbon::now(),
				null // deleted_at
			);

			app(RequestForInformationReplyCreationService::class)->createReply($replyDTO, $this->negotiationId);

			$this->replyBody = '';
		}

		public function uploadRfiDocument():void
		{
			$this->validate([
				'docFile' => 'required|file|max:10240',
				'docName' => 'required|string|max:255',
				'docCategory' => 'nullable|string|max:255',
				'docDescription' => 'nullable|string',
				'docPrivate' => 'boolean',
			]);

			$data = [
				'name' => $this->docName,
				'category' => $this->docCategory,
				'description' => $this->docDescription,
				'is_private' => (bool) $this->docPrivate,
				'uploaded_by_id' => auth()->id(),
				'negotiation_id' => $this->negotiationId,
				'documentable_type' => 'App\\Models\\RequestForInformation',
				'documentable_id' => $this->viewingRfiId,
				'tenant_id' => $this->tenantId,
			];

			app(\App\Services\Document\DocumentStorageService::class)->createRfiDocument($data, $this->viewingRfiId, $this->docFile);

			$this->resetDocForm();
			$this->showUploadDocModal = false;

			if ($this->viewingRfi) {
				$this->viewingRfi->load('documents');
			}
		}

		public function viewRfiDocument(int $documentId):void
		{
			$this->currentDocument = app(\App\Services\Document\DocumentFetchingService::class)->getDocumentById($documentId);
			$this->documentUrl = app(\App\Services\Document\DocumentFetchingService::class)->getDocumentPresignedUrl($documentId,
				300);
			$this->showViewDocModal = true;
		}

		public function deleteRfiDocument(int $documentId):void
		{
			app(\App\Services\Document\DocumentDestructionService::class)->deleteDocument($documentId);
			if ($this->viewingRfi) {
				$this->viewingRfi->load('documents');
			}
			if ($this->currentDocument && $this->currentDocument->id === $documentId) {
				$this->currentDocument = null;
				$this->documentUrl = null;
				$this->showViewDocModal = false;
			}
		}

		public function resetDocForm():void
		{
			$this->reset('docFile');
			$this->docName = '';
			$this->docCategory = null;
			$this->docDescription = null;
			$this->docPrivate = false;
		}

		public function formatFileSize($bytes):string
		{
			$units = ['B', 'KB', 'MB', 'GB', 'TB'];
			$bytes = max($bytes, 0);
			$pow = floor(($bytes? log($bytes) : 0) / log(1024));
			$pow = min($pow, count($units) - 1);
			$bytes /= (1 << (10 * $pow));
			return round($bytes, 2).' '.$units[$pow];
		}

		public function getListeners()
		{
			$tenantId = tenant()->id;

			return [
				"echo-private:private.negotiation.$this->negotiationId.$tenantId,.RfiCreated" => 'handleRfiCreated',
				"echo-private:private.negotiation.$this->negotiationId.$tenantId,.RfiReplyPosted" => 'handleReplyPosted',
			];
		}

		public function handleRfiCreated(array $data)
		{
			$this->loadRfis();
		}

		public function handleReplyPosted(array $data)
		{
			$this->loadReplies();
			$authUserId = auth()->id();

			foreach ($this->rows as &$row) {
				if ($row['id'] === $data['request_for_information_id']) {
					$row['replies_count'] = $data['replies_count'];

					// Check if the authenticated user is a recipient of this RFI
					$recipient = app(RequestForInformationRecipientFetchingService::class)
						->getRecipientByRfiIdAndUserId($row['id'], $authUserId);

					// If the user is a recipient and hasn't read the RFI, update unread count and show alert
					if ($recipient && !$recipient->is_read) {
						$row['unread_replies_count'] = $data['replies_count'];
						$row['show_alert'] = true;
					} // If the user is a recipient and has read the RFI, check for unread replies
					elseif ($recipient) {
						// Get the RFI
						$rfi = app(RequestForInformationFetchingService::class)->getRfiById($row['id']);
						// Count unread replies
						$unreadReplies = $rfi->replies()->where('is_read', false)->count();
						if ($unreadReplies > 0) {
							$row['unread_replies_count'] = $unreadReplies;
							$row['show_alert'] = true;
						}
					}

					break;
				}
			}
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
			$this->showResponsesModal = false;
		}

		public function deleteRfi($rfiId):void
		{
			app(RequestForInformationDestructionService::class)->deleteRfi($rfiId);
			$this->loadRfis();
		}
	}
?>

<div>
	<div class="mb-4 flex justify-between items-center">
		<h2 class="text-xl font-semibold">Requests for Information</h2>
		<x-button
				icon="plus"
				wire:click="openCreateModal"
				sm>Create RFI
		</x-button>
	</div>

	<div class="space-y-4">
		<x-table
				:$headers
				:$rows
				paginate
				:quantity="[2,5,10]"
				filter
				striped>

			@interact('column_action', $row)
			<div class="flex space-x-1">
				<x-button.circle
						sm
						icon="eye"
						wire:click="openResponsesModal({{ is_array($row) ? $row['id'] : $row->id }})"
						title="View Request Details"
				/>
				<x-button.circle
						sm
						icon="pencil-square"
						wire:click="openEditModal({{ is_array($row) ? $row['id'] : $row->id }})"
						title="Edit RFI"
				/>
				<x-button.circle
						sm
						color="red"
						icon="trash"
						wire:click="deleteRfi({{ is_array($row) ? $row['id'] : $row->id }})"
						title="Delete RFI"
				/>
			</div>
			@endinteract

			@interact('column_replies_count', $row)
			@php
				$id   = is_array($row) ? $row['id']   : $row->id;
				$un   = is_array($row) ? $row['unread_replies_count'] : ($row->unread_replies_count ?? 0);
				$all  = is_array($row) ? $row['replies_count']        : ($row->replies_count ?? 0);
				$show = is_array($row) ? $row['show_alert']           : ($row->show_alert ?? false);
			@endphp
			<div class="relative inline">
				<x-button
						color="sky"
						xs
						flat
						class="font-semibold"
						:text="'Replies ('.($un > 0 ? $un : $all).')'"
						wire:click="openResponsesModal({{ $id }})"
				/>
				@if($show)
					<div class="absolute top-0 -right-4">
			                    <span class="relative flex size-3">
			                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
			                        <span class="relative inline-flex size-3 rounded-full bg-rose-500"></span>
			                    </span>
					</div>
				@endif
			</div>
			@endinteract
		</x-table>

	</div>

	<!-- Create RFI Modal -->
	<x-modal
			id="create-rfi-modal"
			wire="showCreateModal"
			x-on:hidden.window="$wire.closeModal()">
		<x-card title="Create New Request for Information">
			<div class="space-y-4">
				<x-input
						label="Title"
						wire:model="title" />
				<x-textarea
						label="Content"
						wire:model="body"
						rows="5" />
				<x-select.styled
						label="Status"
						wire:model="status"
						:options="['Pending', 'Approved', 'Rejected', 'Processing']" />
				<div>
					<x-label>Recipients</x-label>
					<div class="mt-2 space-y-2">
						@foreach($availableUsers as $user)
							<label class="flex items-center">
								<x-checkbox
										wire:model="selectedRecipients"
										value="{{ $user->id }}" />
								<span class="ml-2">
									{{ is_string($user->name) ? $user->name : '' }}
									@if(userRole($user, $negotiationId))
										<x-badge
												text="{{ userRole($user, $negotiationId)->label() }}"
												color="cyan" />
									@endif
								</span>
							</label>
						@endforeach
					</div>
				</div>

				<!-- Optional Document Attachment -->
				<div class="mt-4 border-t pt-4">
					<h3 class="text-sm font-medium text-gray-700 dark:text-dark-200">Attach Document (optional)</h3>
					<div class="mt-3 grid grid-cols-1 gap-3">
						<x-upload
								label="File"
								wire:model="docFile"
								id="neg-rfi-create-doc-file" />
						<x-input
								label="Name"
								wire:model="docName"
								id="neg-rfi-create-doc-name" />
						<x-input
								label="Category"
								wire:model="docCategory"
								id="neg-rfi-create-doc-category" />
						<x-textarea
								label="Description"
								wire:model="docDescription"
								id="neg-rfi-create-doc-description"
								rows="3" />
						<div class="flex items-center">
							<x-checkbox
									label="Private"
									wire:model="docPrivate"
									id="neg-rfi-create-doc-private" />
						</div>
					</div>
				</div>
			</div>

			<x-slot:footer>
				<div class="flex justify-end gap-x-2">
					<x-button
							flat
							wire:click="$set('showCreateModal', false)">Cancel
					</x-button>
					<x-button
							primary
							wire:click="createRfi">Save
					</x-button>
				</div>
			</x-slot:footer>
		</x-card>
	</x-modal>

	<!-- Edit RFI Modal -->
	<x-modal
			id="edit-rfi-modal"
			wire="showEditModal"
			x-on:hidden.window="$wire.closeModal()">
		<x-card title="Edit Request for Information">
			<div class="space-y-4">
				<x-input
						label="Title"
						wire:model="title" />
				<x-textarea
						label="Content"
						wire:model="body"
						rows="5" />
				<x-select.styled
						label="Status"
						wire:model="status"
						:options="['Pending', 'Approved', 'Rejected', 'Processing']" />
				<div>
					<x-label>Recipients</x-label>
					<div class="mt-2 space-y-2">
						@foreach($availableUsers as $user)
							<label class="flex items-center">
								<x-checkbox
										wire:model="selectedRecipients"
										value="{{ $user->id }}" />
								<span class="ml-2">
									{{ is_string($user->name) ? $user->name : '' }}
									@if(userRole($user, $negotiationId))
										<span class="text-sm text-gray-400 ml-1">({{ userRole($user, $negotiationId)->label() }})</span>
									@endif
								</span>
							</label>
						@endforeach
					</div>
				</div>
			</div>

			<x-slot:footer>
				<div class="flex justify-end gap-x-2">
					<x-button
							flat
							wire:click="$set('showEditModal', false)">Cancel
					</x-button>
					<x-button
							primary
							wire:click="updateRfi">Update
					</x-button>
				</div>
			</x-slot:footer>
		</x-card>
	</x-modal>

	<!-- View Request Details and Responses Modal -->
	<x-modal
			size="6xl"
			id="view-responses-modal"
			wire="showResponsesModal"
			x-on:hidden.window="$wire.closeModal()">
		<x-card title="Request Details & Responses">
			<div class="space-y-6">
				<!-- Request Details Section -->
				@if($viewingRfi)
					<div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4 border border-gray-200 dark:border-dark-600">
						<h3 class="text-lg font-semibold mb-2">{{ $viewingRfi->title }}</h3>
						<div class="flex items-center mb-3">
							<span
									class="px-2 py-1 text-xs rounded-full
								@if($viewingRfi->status == 'Approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
								@elseif($viewingRfi->status == 'Rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
								@elseif($viewingRfi->status == 'Processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
								@else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
								@endif">
								{{ $viewingRfi->status }}
							</span>
							<span class="text-xs text-gray-500 dark:text-dark-300 ml-3">
								Created {{ $viewingRfi->created_at->format('Y-m-d H:i') }}
							</span>
						</div>
						<div class="prose dark:prose-invert max-w-none">
							<p>{{ $viewingRfi->body }}</p>
						</div>
						<div class="mt-3 text-sm text-gray-500 dark:text-dark-300">
							<p>From: {{ $viewingRfi->sender->name }}</p>
						</div>
					</div>
				@endif

				<!-- Documents Section -->
				<div>
					<h4 class="text-md font-semibold mb-3 text-gray-700 dark:text-dark-200">Documents</h4>
					<div class="mt-2 flow-root overflow-hidden rounded-lg border border-gray-200 dark:border-dark-600">
						<table class="w-full text-left">
							<thead class="bg-gray-50 dark:bg-dark-700">
							<tr>
								<th class="px-3 py-2 text-xs font-semibold">Name</th>
								<th class="hidden md:table-cell px-3 py-2 text-xs font-semibold">Type</th>
								<th class="hidden md:table-cell px-3 py-2 text-xs font-semibold">Size</th>
								<th class="px-3 py-2 text-xs font-semibold">Category</th>
								<th class="py-2 pl-3 text-right">
									<x-button.circle
											wire:click="$set('showUploadDocModal', true)"
											sm
											flat
											icon="plus" />
								</th>
							</tr>
							</thead>
							<tbody>
							@forelse(($viewingRfi?->documents ?? []) as $document)
								<tr>
									<td class="px-3 py-2 text-xs font-medium">{{ $document->name }}</td>
									<td class="hidden md:table-cell px-3 py-2 text-xs text-gray-500 dark:text-dark-300">{{ $document->file_type }}</td>
									<td class="hidden md:table-cell px-3 py-2 text-xs text-gray-500 dark:text-dark-300">{{ $this->formatFileSize($document->file_size) }}</td>
									<td class="px-3 py-2 text-xs text-gray-500 dark:text-dark-300">{{ $document->category }}</td>
									<td class="px-3 py-2 text-right">
										<x-button.circle
												wire:click="viewRfiDocument({{ $document->id }})"
												flat
												color="sky"
												icon="eye"
												sm />
										<x-button.circle
												wire:click="deleteRfiDocument({{ $document->id }})"
												flat
												color="red"
												icon="trash"
												sm />
									</td>
								</tr>
							@empty
								<tr>
									<td
											colspan="5"
											class="text-center p-4 text-gray-500">No documents attached.
									</td>
								</tr>
							@endforelse
							</tbody>
						</table>
					</div>

					<h4 class="text-md font-semibold mt-6 mb-3 text-gray-700 dark:text-dark-200">Responses</h4>
					@if($viewingRfiId && count($replies) > 0)
						<div class="space-y-4">
							@foreach($replies as $reply)
								<div class="border rounded-lg p-4 border-gray-200 dark:border-dark-600">
									<div class="flex justify-between items-start">
										<div>
											<p class="font-semibold text-gray-800 dark:text-dark-100">{{ is_string($reply->user->name) ? $reply->user->name : '' }}</p>
											<p class="text-sm text-gray-500 dark:text-dark-300">{{ $reply->created_at ? $reply->created_at->format('Y-m-d H:i') : '' }}</p>
										</div>
									</div>
									<div class="mt-2">
										<p class="text-gray-700 dark:text-dark-200">{{ is_string($reply->body) ? $reply->body : '' }}</p>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<p class="text-center text-gray-500 dark:text-dark-300">No responses yet.</p>
					@endif

					<div class="mt-4">
						<x-textarea
								label="Your Response"
								wire:model="replyBody"
								rows="3" />
					</div>
				</div>
			</div>

			<x-slot:footer>
				<div class="flex justify-end gap-x-2">
					<x-button
							flat
							wire:click="$set('showResponsesModal', false)">Close
					</x-button>
					<x-button
							primary
							wire:click="submitReply">Submit Response
					</x-button>
				</div>
			</x-slot:footer>
		</x-card>
	</x-modal>

	<!-- Upload RFI Document Modal -->
	<x-modal wire="showUploadDocModal">
		<x-card title="Upload Document">
			<div class="space-y-4">
				<div>
					<x-upload
							label="File"
							wire:model="docFile"
							id="neg-rfi-doc-file"
							class="mt-1 block w-full" />
				</div>
				<div>
					<x-input
							label="Name"
							wire:model="docName"
							id="neg-rfi-doc-name"
							class="mt-1 block w-full" />
				</div>
				<div>
					<x-input
							label="Category"
							wire:model="docCategory"
							id="neg-rfi-doc-category"
							class="mt-1 block w-full" />
				</div>
				<div>
					<x-textarea
							label="Description"
							wire:model="docDescription"
							id="neg-rfi-doc-description"
							class="mt-1 block w-full" />
				</div>
				<div class="flex items-center">
					<x-checkbox
							label="Private"
							wire:model="docPrivate"
							id="neg-rfi-doc-private" />
				</div>
			</div>
			<x-slot:footer>
				<div class="flex justify-end gap-x-4">
					<x-button
							flat
							text="Cancel"
							wire:click="$toggle('showUploadDocModal')" />
					<x-button
							primary
							label="Upload"
							text="Upload"
							wire:click="uploadRfiDocument"
							wire:loading.attr="disabled" />
				</div>
			</x-slot:footer>
		</x-card>
	</x-modal>

	<!-- View RFI Document Modal -->
	<x-modal
			wire="showViewDocModal"
			max-width="4xl">
		<x-card title="{{ $currentDocument ? $currentDocument->name : 'Document' }}">
			@if($currentDocument && $documentUrl)
				<div class="space-y-4">
					<div class="flex justify-between">
						<div>
							<p class="text-sm text-gray-500">Type: {{ $currentDocument->file_type }}</p>
							<p class="text-sm text-gray-500">
								Size: {{ $this->formatFileSize($currentDocument->file_size) }}</p>
							<p class="text-sm text-gray-500">
								Uploaded: {{ $currentDocument->created_at->format('M d, Y') }}</p>
						</div>
						<div>
							<x-button
									href="{{ $documentUrl }}"
									target="_blank"
									primary
									label="Expand"
									icon="arrows-pointing-out" />
						</div>
					</div>
					<div class="border rounded-lg p-4 bg-gray-50 dark:bg-dark-800">
						@if(Str::startsWith($currentDocument->file_type, 'image/'))
							<img
									src="{{ $documentUrl }}"
									alt="{{ $currentDocument->name }}"
									class="max-w-full h-auto mx-auto" />
						@elseif(Str::startsWith($currentDocument->file_type, 'application/pdf'))
							<iframe
									src="{{ $documentUrl }}"
									class="w-full h-96"
									frameborder="0"></iframe>
						@else
							<div class="text-center py-8">
								<p>Preview not available for this file type.</p>
								<p class="mt-2">Please download the file to view it.</p>
							</div>
						@endif
					</div>
					@if($currentDocument->description)
						<div>
							<h3 class="text-sm font-medium">Description</h3>
							<p class="mt-1 text-sm text-gray-500">{{ $currentDocument->description }}</p>
						</div>
					@endif
				</div>
			@else
				<div class="py-8 text-center">
					<p>Document not found or unable to generate preview.</p>
				</div>
			@endif
			<x-slot:footer>
				<div class="flex justify-end">
					<x-button
							flat
							label="Close"
							x-on:click="close" />
				</div>
			</x-slot:footer>
		</x-card>
	</x-modal>
</div>
