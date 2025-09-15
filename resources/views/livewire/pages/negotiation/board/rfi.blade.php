<?php

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

	new class extends Component {
		public $rfis = [];
		public $headers = [];
		public $rows = [];
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
		public int $tenantId;
		public $sortField = 'created_at';
		public $sortDirection = 'desc';

		public function mount($negotiationId = null)
		{
			$this->negotiationId = $negotiationId;
			$this->tenantId = tenant()->id;
			$this->loadRfis();
			$this->loadAvailableUsers();

			$this->headers = [
				['index' => 'id', 'label' => '#', 'sortable' => true],
				['index' => 'title', 'label' => 'Title', 'sortable' => true],
				['index' => 'status', 'label' => 'Status', 'sortable' => true],
				['index' => 'replies_count', 'label' => 'Responses', 'sortable' => true],
				['index' => 'created_at', 'label' => 'Created', 'sortable' => true],
				['index' => 'action', 'label' => 'Action']
			];
		}

		public function loadRfis()
		{
			if ($this->negotiationId) {
				$this->rfis = app(RequestForInformationFetchingService::class)->getRfisByNegotiationId($this->negotiationId);
			} else {
				$this->rfis = app(RequestForInformationFetchingService::class)->getAllRfis();
			}

			$this->prepareRows();
		}

		public function prepareRows()
		{
			$this->rows = [];

			foreach ($this->rfis as $rfi) {
				$repliesCount = $rfi->replies->count();

				$this->rows[] = [
					'id' => $rfi->id,
					'title' => $rfi->title,
					'status' => $rfi->status,
					'replies_count' => $repliesCount,
					'created_at' => $rfi->created_at->format('Y-m-d H:i'),
					'rfi' => $rfi
				];
			}

			// Sort the rows
			if ($this->sortField && $this->sortDirection) {
				$this->rows = collect($this->rows)->sortBy([
					[$this->sortField, $this->sortDirection]
				])->values()->all();
			}
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
			// In a real implementation, you would load users from the database
			// For now, we'll use a simple array of users
			$this->availableUsers = \App\Models\User::where('tenant_id', $this->tenantId)->get();
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

				app(RequestForInformationRecipientCreationService::class)->createRecipient($recipientDTO);
			}

			$this->reset('title', 'body', 'status', 'selectedRecipients');
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

			$this->loadReplies();
			$this->showResponsesModal = true;

			// Mark as read if current user is a recipient
			$recipient = app(RequestForInformationRecipientFetchingService::class)
				->getRecipientByRfiIdAndUserId($rfiId, auth()->id());

			if ($recipient && !$recipient->is_read) {
				app(RequestForInformationRecipientUpdateService::class)->updateReadStatus($recipient->id, true);
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
				Carbon::now(),
				Carbon::now(),
				null // deleted_at
			);

			app(RequestForInformationReplyCreationService::class)->createReply($replyDTO);

			$this->replyBody = '';
			$this->loadReplies();
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
				striped>

			@interact('column_action', $row)
			<div class="flex space-x-1">
				<x-button.circle
						sm
						icon="eye"
						wire:click="openResponsesModal({{ $row['id'] }})"
						title="View Request Details"
				/>
				<x-button.circle
						sm
						icon="pencil-square"
						wire:click="openEditModal({{ $row['id'] }})"
						title="Edit RFI"
				/>
				<x-button.circle
						sm
						color="red"
						icon="trash"
						wire:click="deleteRfi({{ $row['id'] }})"
						title="Delete RFI"
				/>
			</div>
			@endinteract

			@interact('column_replies_count', $row)
			<x-link
					class="font-semibold"
					wire:click="openResponsesModal({{ $row['id'] }})"
					href="javascript:void(0)">View Responses ({{ $row['replies_count'] }})
			</x-link>
			@endinteract

			@interact('column_test', $column)

			@php
				// Deep resolve: execute any Closures (even nested in arrays), and normalize to safe strings for known text keys.
				$deepResolve = function ($v) use (&$deepResolve) {
					while ($v instanceof \Closure) {
						$v = $v();
					}
					if (is_array($v)) {
						return array_map($deepResolve, $v);
					}
					if ($v instanceof \Illuminate\Contracts\Support\Htmlable) {
						// leave as Htmlable; we'll render with {!! !!} where appropriate
						return $v;
					}
					if ($v instanceof \Stringable) {
						return (string) $v;
					}
					return is_scalar($v) ? $v : '';
				};

				$column = $deepResolve($column);

				// Pull common fields as strings
				$label    = $column['label']   instanceof \Illuminate\Contracts\Support\Htmlable ? $column['label']->toHtml() : (string) ($column['label'] ?? '');
				$index    = (string) ($column['index'] ?? '');
				$sortable = (bool)   ($column['sortable'] ?? false);
			@endphp

			@if($sortable)
				<div
						class="flex items-center cursor-pointer"
						@if($index !== '')
							wire:click="sort('{{ e($index) }}')"
						@endif
				>
					Label: already html-escaped or html-safe
					{!! $label !!}

					@if($sortField === $index)
						<span class="ml-1">
			                    @if($sortDirection === 'asc')
								<svg
										xmlns="http://www.w3.org/2000/svg"
										class="h-4 w-4"
										fill="none"
										viewBox="0 0 24 24"
										stroke="currentColor">
			                            <path
					                            stroke-linecap="round"
					                            stroke-width="2"
					                            d="M5 15l7-7 7 7" />
			                        </svg>
							@else
								<svg
										xmlns="http://www.w3.org/2000/svg"
										class="h-4 w-4"
										fill="none"
										viewBox="0 0 24 24"
										stroke="currentColor">
			                            <path
					                            stroke-linecap="round"
					                            stroke-linejoin="round"
					                            stroke-width="2"
					                            d="M19 9l-7 7-7-7" />
			                        </svg>
							@endif
			                </span>
					@endif
				</div>
			@else
				{!! $label !!}
			@endif

			@endinteract


		</x-table>

		@if(count($rows) === 0)
			<div class="text-center py-4">
				<p class="text-gray-500">No requests for information found. Click "Create RFI" to create one.</p>
			</div>
		@endif
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
								<span class="ml-2">{{ is_string($user->name) ? $user->name : '' }}</span>
							</label>
						@endforeach
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
								<span class="ml-2">{{ is_string($user->name) ? $user->name : '' }}</span>
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

				<!-- Responses Section -->
				<div>
					<h4 class="text-md font-semibold mb-3 text-gray-700 dark:text-dark-200">Responses</h4>
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
</div>