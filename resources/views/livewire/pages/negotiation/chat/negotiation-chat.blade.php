<?php

	use App\DTOs\Conversation\ConversationDTO;
	use App\DTOs\Message\MessageDTO;
	use App\DTOs\MessageReaction\MessageReactionDTO;
	use App\Events\Conversation\ConversationCreatedEvent;
	use App\Models\Conversation;
	use App\Models\Message;
	use App\Models\User;
	use App\Services\Chat\ChatService;
	use App\Services\Conversation\ConversationCreationService;
	use App\Services\Conversation\ConversationDestructionService;
	use App\Services\Conversation\ConversationFetchingService;
	use App\Services\Conversation\ConversationReadService;
	use App\Services\Message\MessageCreationService;
	use App\Services\Message\MessageReactionService;
	use Illuminate\Database\Eloquent\Collection;
	use Livewire\Attributes\Computed;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;

	new class extends Component {
		/**
		 * The active tab (public, private, or group)
		 *
		 * @var string
		 */
		public string $activeTab = 'public';

		public int $negotiationId;

		public int $listVersion = 0;

		public array $unread = [];

		/**
		 * The message input text
		 *
		 * @var string
		 */
		public string $messageInput = '';

		/**
		 * The selected document for attachment
		 *
		 * @var array|null
		 */
		public $documentToAttach = null;

		/**
		 * The ID of the selected conversation
		 *
		 * @var int|null
		 */
		public ?int $selectedConversationId = null;

		/**
		 * The ID of the user to whisper to
		 *
		 * @var int|null
		 */
		public ?int $whisperToUserId = null;

		/**
		 * Whether the message is a whisper
		 *
		 * @var bool
		 */
		public bool $isWhisper = false;

		/**
		 * Whether the message is urgent (alert)
		 *
		 * @var bool
		 */
		public bool $isUrgent = false;

		/**
		 * Whether the message is an emergency (danger)
		 *
		 * @var bool
		 */
		public bool $isEmergency = false;

		/**
		 * Whether to show the new conversation modal
		 *
		 * @var bool
		 */
		public bool $showNewConversationModal = false;

		/**
		 * The type of the new conversation (private or group)
		 *
		 * @var string
		 */
		public string $newConversationType = 'private';

		/**
		 * The name of the new conversation (for group conversations)
		 *
		 * @var string
		 */
		public string $newConversationName = '';

		/**
		 * The IDs of the users selected for the new conversation
		 *
		 * @var array
		 */
		public array $selectedUserIds = [];

		public ?int $previousConversationId = null;

		/**
		 * Initialize the component
		 *
		 * Ensures the public conversation exists and sets it as the selected conversation
		 *
		 * @param  int  $negotiationId
		 *
		 * @return void
		 */
		public function mount(int $negotiationId):void
		{
			// set first
			$this->negotiationId = $negotiationId;

			// seed unread correctly
			$this->unread = app(\App\Services\Conversation\ConversationReadService::class)
				->seedUnreadForNegotiation(auth()->user(), $negotiationId);

			// ensure public for THIS negotiation
			$public = app(\App\Services\Conversation\ConversationCreationService::class)->ensurePublicConversation(
				auth()->user()->tenant_id,
				auth()->id(),
				$negotiationId
			);

			// default selection only if nothing already selected
			$this->selectedConversationId ??= $public->id;

			// clear unread & persist for the opened thread
			if ($this->selectedConversationId) {
				$this->unread[$this->selectedConversationId] = 0;
				app(\App\Services\Conversation\ConversationReadService::class)
					->markConversationRead(auth()->user(), $this->selectedConversationId);
			}
		}

		public function openConversation(int $conversationId):void
		{
			$this->selectedConversationId = $conversationId;
			$this->unread[$conversationId] = 0;
			app(\App\Services\Conversation\ConversationReadService::class)
				->markConversationRead(auth()->user(), $conversationId);
			$this->listVersion++;
			$this->dispatch('new-message');
		}

		#[Computed]
		public function tabUnread():array
		{
			$publicId = optional($this->publicConversation())->id; // ← must match the id used in unread[]
			$privateIds = collect($this->privateConversations())->pluck('id')->all();
			$groupIds = collect($this->groupConversations())->pluck('id')->all();

			$sum = fn(array $ids) => array_sum(array_intersect_key($this->unread, array_flip($ids)));

			return [
				'public' => $publicId? ($this->unread[$publicId] ?? 0) : 0,
				'private' => $sum($privateIds),
				'group' => $sum($groupIds),
			];
		}

		public function updatingSelectedConversationId(int $value):void
		{
			$this->previousConversationId = $this->selectedConversationId;
		}

		public function updatedSelectedConversationId(int $value):void
		{
			// Let Alpine know to switch presence rooms
			$this->dispatch('conversation-changed', oldId: $this->previousConversationId, newId: $value);
		}


		/**
		 * Get the public conversation for the current tenant and negotiation
		 *
		 * @return Conversation|null The public conversation or null if not found
		 */
		#[Computed]
		public function publicConversation():?Conversation
		{
			$this->listVersion;
			return app(ConversationFetchingService::class)->getPublicConversation(auth()->user()->tenant_id,
				$this->negotiationId);
		}

		/**
		 * Get the private conversations for the current user in the current negotiation
		 *
		 * @return Collection Collection of private conversations
		 */
		#[Computed]
		public function privateConversations():Collection
		{
			$this->listVersion;
			return app(ConversationFetchingService::class)->getPrivateConversations(auth()->user(),
				$this->negotiationId);
		}

		/**
		 * Get the group conversations for the current user in the current negotiation
		 *
		 * @return Collection Collection of group conversations
		 */
		#[Computed]
		public function groupConversations():Collection
		{
			$this->listVersion;
			return app(ConversationFetchingService::class)->getGroupConversations(auth()->user(), $this->negotiationId);
		}

		/**
		 * Get the current conversation based on the selected conversation ID or active tab
		 * Ensures the conversation belongs to the current negotiation
		 *
		 * @return Conversation|null The current conversation or null if not found
		 */
		#[Computed]
		public function currentConversation():?Conversation
		{
			$conversationFetchingService = app(ConversationFetchingService::class);

			if ($this->selectedConversationId) {
				return $conversationFetchingService->getConversationById($this->selectedConversationId,
					$this->negotiationId);
			}

			if ($this->activeTab === 'public') {
				return $this->publicConversation();
			}

			return null;
		}

		/**
		 * Get the messages for the current conversation
		 *
		 * @return Collection Collection of messages
		 */
		#[Computed]
		public function messages():Collection
		{
			return app(ChatService::class)->getMessages($this->currentConversation());
		}

		/**
		 * Get the users participating in the current conversation
		 *
		 * @return Collection Collection of users
		 */
		#[Computed]
		public function conversationUsers():Collection
		{
			return app(ChatService::class)->getConversationUsers($this->currentConversation());
		}

		/**
		 * Get the available users for creating new conversations
		 *
		 * @return Collection Collection of users
		 */
		#[Computed]
		public function availableUsers():Collection
		{
			return app(ChatService::class)->getAvailableUsers(auth()->user()->tenant_id);
		}

		/**
		 * Get the other user in a private conversation
		 *
		 * @param  Conversation  $conversation  The private conversation
		 *
		 * @return User|null The other user or null if not found
		 */
		public function getOtherUserInPrivateConversation(Conversation $conversation):?User
		{
			return app(ChatService::class)->getOtherUserInPrivateConversation($conversation, auth()->id());
		}

		/**
		 * Get the count of active users in a conversation
		 *
		 * @param  Conversation  $conversation  The conversation
		 *
		 * @return int The count of active users
		 */
		public function getActiveUsersCount(Conversation $conversation):int
		{
			return app(ChatService::class)->getActiveUsersCount($conversation);
		}

		/**
		 * Send a message in the current conversation
		 *
		 * @return void
		 */
		public function sendMessage():void
		{
			if (empty(trim($this->messageInput)) && !$this->documentToAttach || !$this->currentConversation()) {
				return;
			}

			// Prepare message content with status indicators if needed
			$content = $this->messageInput;
			if ($this->isUrgent) {
				$content = '[URGENT] '.$content;
			} elseif ($this->isEmergency) {
				$content = '[EMERGENCY] '.$content;
			}

			$messageData = [
				'conversation_id' => $this->currentConversation()->id,
				'negotiation_id' => $this->negotiationId,
				'tenant_id' => tenant()->id,
				'user_id' => auth()->id(),
				'content' => encrypt($content),
				'is_whisper' => $this->isWhisper,
			];

			if ($this->isWhisper && $this->whisperToUserId) {
				$messageData['whisper_to'] = $this->whisperToUserId;
			}

			$messageDTO = MessageDTO::fromArray($messageData);
			$message = app(MessageCreationService::class)->createMessage($messageDTO);

			// Attach document if one is selected
			if ($this->documentToAttach) {
				$this->attachDocumentToMessage($message->id, $this->documentToAttach['id']);
			}

			$this->reset('messageInput', 'isWhisper', 'whisperToUserId', 'isUrgent', 'isEmergency', 'documentToAttach');

			// Dispatch event to trigger scroll
			$this->dispatch('new-message');
		}

		/**
		 * Toggle a reaction on a message
		 */
		public function toggleReaction(int $messageId, string $reactionType):void
		{
			$messageReactionData = [
				'message_id' => $messageId,
				'user_id' => auth()->id(),
				'tenant_id' => tenant()->id,
				'negotiation_id' => $this->negotiationId,
				'reaction_type' => $reactionType,
			];

			$messageReactionDTO = MessageReactionDTO::fromArray($messageReactionData);
			app(MessageReactionService::class)->toggleReaction($messageReactionDTO);
		}

		/**
		 * Get reactions for a message grouped by type
		 */
		public function getMessageReactions(int $messageId):array
		{
			return app(MessageReactionService::class)->getReactionsGroupedByType($messageId);
		}

//		/**
//		 * Check if the current user has reacted to a message with a specific reaction type
//		 */
//		public function hasUserReacted(int $messageId, string $reactionType):bool
//		{
//			return app(\App\Contracts\MessageReactionRepositoryInterface::class)
//				->hasUserReacted($messageId, auth()->id(), $reactionType);
//		}
//
		/**
		 * Select a conversation by ID
		 *
		 * @param  int  $conversationId  The ID of the conversation to select
		 *
		 * @return void
		 */
		public function selectConversation(int $conversationId):void
		{
			$this->selectedConversationId = $conversationId;
			// reset unread count and mark as read for current user
			unset($this->unread[$conversationId]);
			app(ConversationReadService::class)->markConversationRead(auth()->user(), $conversationId);
			// bump list version so lists refresh if relying on it
			$this->listVersion++;
		}

		/**
		 * Set the active tab and select the appropriate conversation
		 *
		 * @param  string  $tab  The tab to set as active
		 *
		 * @return void
		 */
		public function setActiveTab(string $tab):void
		{
			$this->activeTab = $tab;

			// If the public tab is selected, automatically select the public conversation
			if ($tab === 'public') {
				$publicConversation = $this->publicConversation();
				if ($publicConversation) {
					$this->selectedConversationId = $publicConversation->id;
					return;
				}
			}

			// For other tabs, clear the selected conversation
			$this->selectedConversationId = null;
		}

		/**
		 * Toggle whisper mode for a user
		 *
		 * @param  int  $userId  The ID of the user to whisper to
		 *
		 * @return void
		 */
		public function toggleWhisper(int $userId):void
		{
			if ($this->whisperToUserId === $userId && $this->isWhisper) {
				$this->isWhisper = false;
				$this->whisperToUserId = null;
			} else {
				$this->isWhisper = true;
				$this->whisperToUserId = $userId;
			}
		}

		/**
		 * Open the new conversation modal
		 *
		 * @return void
		 */
		public function openNewConversationModal():void
		{
			$this->showNewConversationModal = true;
		}

		/**
		 * Close the new conversation modal and reset form fields
		 *
		 * @return void
		 */
		public function closeNewConversationModal():void
		{
			$this->showNewConversationModal = false;
			$this->reset('newConversationType', 'newConversationName', 'selectedUserIds');
		}

		/**
		 * Create a new conversation
		 *
		 * @return void
		 */
		public function createNewConversation():void
		{
			if ($this->newConversationType === 'private' && count($this->selectedUserIds) !== 1) {
				// Private conversations must have exactly one other user
				return;
			}

			if ($this->newConversationType === 'group' && empty($this->selectedUserIds)) {
				// Group conversations must have at least one other user
				return;
			}

			if ($this->newConversationType === 'group' && empty(trim($this->newConversationName))) {
				// Group conversations must have a name
				return;
			}

			$conversationData = [
				'tenant_id' => auth()->user()->tenant_id,
				'created_by' => auth()->id(),
				'negotiation_id' => $this->negotiationId,
				'type' => $this->newConversationType,
				'name' => $this->newConversationType === 'group'? $this->newConversationName : null,
			];

			$conversation = app(ConversationCreationService::class)->createConversationWithUsers(
				$conversationData,
				$this->selectedUserIds
			);

			$this->closeNewConversationModal();
			$this->setActiveTab($this->newConversationType);
			$this->selectConversation($conversation->id);
		}

		/**
		 * Close a conversation (mark as inactive)
		 *
		 * @param  int  $conversationId  The ID of the conversation to close
		 *
		 * @return void
		 */
		public function closeConversation(int $conversationId):void
		{
			$closed = app(ConversationDestructionService::class)->closeConversation($conversationId, auth()->id());

			if ($closed && $this->selectedConversationId === $conversationId) {
				$this->selectedConversationId = null;
			}
		}

		/**
		 * Get the event listeners for the component
		 *
		 * @return array The event listeners
		 */
		public function getListeners():array
		{
			$listeners = [
				// negotiation-wide presence channel (singular)
				"echo-presence:negotiation.{$this->negotiationId},.ConversationCreated" => 'handleConversationCreated',
				"echo-presence:negotiation.{$this->negotiationId},.ConversationClosed" => 'handleConversationClosed',

				// optional but recommended for list badges/snippets
				"echo-presence:negotiation.{$this->negotiationId},.MessageSent" => 'handleMessageSent',

				// Document and reaction events
				"echo-presence:negotiation.{$this->negotiationId},.DocumentAttached" => 'handleDocumentAttached',
				"echo-presence:negotiation.{$this->negotiationId},.ReactionAdded" => 'handleReactionAdded',
				"echo-presence:negotiation.{$this->negotiationId},.ReactionRemoved" => 'handleReactionRemoved',
			];

			// live stream for the currently open thread
			if ($this->selectedConversationId) {
				$listeners["echo-private:conversation.{$this->selectedConversationId},.MessageSent"] = 'handleMessageSent';
				$listeners["echo-private:conversation.{$this->selectedConversationId},.DocumentAttached"] = 'handleDocumentAttached';
				$listeners["echo-private:conversation.{$this->selectedConversationId},.ReactionAdded"] = 'handleReactionAdded';
				$listeners["echo-private:conversation.{$this->selectedConversationId},.ReactionRemoved"] = 'handleReactionRemoved';
			}

			return $listeners;
		}


		/**
		 * Handle a message sent event
		 *
		 * @param  array  $data  The event data
		 *
		 * @return void
		 */
		public function handleMessageSent(array $data):void
		{
			$conversationId = (int) ($data['conversation_id'] ?? 0);
			$messageId = (int) ($data['message_id'] ?? 0);
			$senderId = (int) ($data['sender_id'] ?? 0);

			if (!$conversationId) return;

			static $seen = [];
			if ($messageId && isset($seen[$messageId])) return;
			if ($messageId) {
				$seen[$messageId] = true;
				if (count($seen) > 500) array_shift($seen);
			}

			// active thread
			if ($conversationId === (int) $this->selectedConversationId) {
				if ($senderId !== (int) auth()->id()) {
					$this->unread[$conversationId] = 0;
					app(ConversationReadService::class)->markConversationRead(auth()->user(), $conversationId);
				}
				$this->dispatch('new-message');
				return;
			}

			// other thread
			if ($senderId !== (int) auth()->id()) {
				$this->unread[$conversationId] = ($this->unread[$conversationId] ?? 0) + 1;
				$this->dispatch('notify', [
					'title' => $data['conversation_name'] ?? 'New Message',
					'message' => 'New message in '.($data['conversation_name'] ?? 'a conversation'),
				]);
			}

			$this->listVersion++; // refresh list/badges
		}

		/**
		 * Handle a conversation created event
		 *
		 * @param  array  $data  The event data
		 *
		 * @return void
		 */
		public function handleConversationCreated(array $data):void
		{
			$this->listVersion++;

			// Optionally set initial unread=0 for the new conversation
			if ($id = (int) ($data['conversation_id'] ?? 0)) {
				$this->unread[$id] = 0;

				// Force a re-render to ensure the UI is updated
				$this->dispatch('conversation-list-updated');
			}
		}

		/**
		 * Handle a document attached event
		 *
		 * @param  array  $data  The event data
		 *
		 * @return void
		 */
		public function handleDocumentAttached(array $data):void
		{
			$conversationId = (int) ($data['conversation_id'] ?? 0);
			$messageId = (int) ($data['message_id'] ?? 0);
			$senderId = (int) ($data['sender_id'] ?? 0);

			if (!$conversationId || !$messageId) return;

			// If this is the active conversation, trigger a refresh
			if ($conversationId === (int) $this->selectedConversationId) {
				$this->dispatch('new-message');
			}
		}

		/**
		 * Handle a reaction added event
		 *
		 * @param  array  $data  The event data
		 *
		 * @return void
		 */
		public function handleReactionAdded(array $data):void
		{
			$conversationId = (int) ($data['conversation_id'] ?? 0);
			$messageId = (int) ($data['message_id'] ?? 0);
			$userId = (int) ($data['user_id'] ?? 0);

			if (!$conversationId || !$messageId) return;

			// If this is the active conversation, trigger a refresh
			if ($conversationId === (int) $this->selectedConversationId) {
				$this->dispatch('new-message');
			}
		}

		/**
		 * Handle a reaction removed event
		 *
		 * @param  array  $data  The event data
		 *
		 * @return void
		 */
		public function handleReactionRemoved(array $data):void
		{
			$conversationId = (int) ($data['conversation_id'] ?? 0);
			$messageId = (int) ($data['message_id'] ?? 0);
			$userId = (int) ($data['user_id'] ?? 0);

			if (!$conversationId || !$messageId) return;

			// If this is the active conversation, trigger a refresh
			if ($conversationId === (int) $this->selectedConversationId) {
				$this->dispatch('new-message');
			}
		}
	}


?>

<div
		x-data="presenceStore({
      authId: @js((int) auth()->id()),
      negotiationId: @js($negotiationId),
      initialConversationId: @js($selectedConversationId),
      usePhpNotifications: false
  })"
		x-init="init()"
		class="h-full bg-white dark:bg-dark-800 rounded-lg p-2 shadow-sm">
	@php use Illuminate\Support\Js; @endphp
	<div
			class="flex flex-col h-full justify-between">
		<!-- Header with tabs -->
		<div class="flex items-center justify-between gap-4 border-b border-gray-200 dark:border-dark-400 pb-2">
			<div class="flex items-center gap-4">
				<!-- Mobile dropdown -->
				<div class="grid grid-cols-1 sm:hidden">
					<select
							wire:model.live="activeTab"
							aria-label="Select a tab"
							class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-2 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
						<option value="public">Public</option>
						<option value="private">Private</option>
						<option value="group">Group</option>
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

				<!-- Desktop tabs -->
				<div class="hidden sm:flex space-x-8">
					<button
							wire:click="setActiveTab('public')"
							class="group inline-flex items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer
                            {{ $activeTab === 'public' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400' }}">
						<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="1.5"
								stroke="currentColor"
								class="size-5">
							<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
						</svg>
						<span class="ml-2">Public</span>
						@if((($this->tabUnread['public'] ?? 0) > 0))
							<span class="ml-2 inline-flex min-w-5 h-5 items-center justify-center rounded-full bg-primary-600 text-white text-[10px] px-1.5">
                                {{ $this->tabUnread['public'] }}
                            </span>
						@endif
					</button>

					<button
							wire:click="setActiveTab('private')"
							class="group inline-flex items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer
                            {{ $activeTab === 'private' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400' }}">
						<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="1.5"
								stroke="currentColor"
								class="size-5">
							<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
						</svg>
						<span class="ml-2">Private</span>
						@if((($this->tabUnread['private'] ?? 0) > 0))
							<span class="ml-2 inline-flex min-w-5 h-5 items-center justify-center rounded-full bg-primary-600 text-white text-[10px] px-1.5">
                                {{ $this->tabUnread['private'] }}
                            </span>
						@endif
					</button>

					<button
							wire:click="setActiveTab('group')"
							class="group inline-flex items-center border-b-2 px-1 py-2 text-sm font-medium hover:cursor-pointer
                            {{ $activeTab === 'group' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-700 hover:text-gray-900 hover:border-gray-300 dark:text-dark-300 dark:hover:border-dark-400 dark:hover:text-dark-400' }}">
						<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="1.5"
								stroke="currentColor"
								class="size-5">
							<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
						</svg>
						<span class="ml-2">Group</span>
						@if((($this->tabUnread['group'] ?? 0) > 0))
							<span class="ml-2 inline-flex min-w-5 h-5 items-center justify-center rounded-full bg-primary-600 text-white text-[10px] px-1.5">
                                {{ $this->tabUnread['group'] }}
                            </span>
						@endif
					</button>
				</div>
			</div>

			<!-- New conversation button -->
			<div class="flex items-center gap-4">
				<x-button
						wire:click="openNewConversationModal"
						sm
						icon="plus">New
				</x-button>
			</div>
		</div>

		<!-- Main content area -->
		<div class="flex h-full overflow-hidden">
			<!-- Sidebar with conversation list -->
			<div class="w-1/4 border-r border-gray-200 dark:border-dark-400 pr-2 overflow-y-auto hidden md:block">
				@if($activeTab === 'public')
					<div class="py-2 relative">
						<div class="font-medium text-sm text-gray-500 dark:text-dark-300 mb-2">Public Chat</div>
						@php
							$publicConv = $this->publicConversation();
						@endphp
						@if($publicConv)
							<div
									wire:click="selectConversation({{ $publicConv->id }})"
									class="p-2 rounded-md cursor-pointer hover:bg-gray-100 dark:hover:bg-dark-600 {{ $selectedConversationId === $publicConv->id ? 'bg-gray-100 dark:bg-dark-600 text-dark-800 dark:text-dark-200 border-l-4 border-primary-400 dark:border-primary-500' : '' }}">
								<div class="font-medium text-sm">{{ $publicConv->name }}</div>
								<div class="text-xs text-gray-500 dark:text-dark-300">
									{{ $this->getActiveUsersCount($publicConv) }} participants
								</div>
							</div>
						@else
							<div class="text-sm text-gray-500 dark:text-dark-300">No public chat available</div>
						@endif
					</div>
				@elseif($activeTab === 'private')
					<div class="py-2">
						<div class="font-medium text-sm text-gray-500 dark:text-dark-300 mb-2">Private Chats</div>
						@php
							$privateConvs = $this->privateConversations();
						@endphp
						@if($privateConvs->count() > 0)
							@foreach($privateConvs as $conversation)
								<div
										wire:click="selectConversation({{ $conversation->id }})"
										class="p-2 rounded-md cursor-pointer hover:bg-gray-100 dark:hover:bg-dark-600 {{ $selectedConversationId === $conversation->id ? 'bg-gray-100 dark:bg-dark-600 border-l-4 border-primary-400 dark:border-primary-500' : '' }}">
									<div class="flex justify-between items-center">
										<div class="font-medium text-sm text-dark-700 dark:text-white">
											@php
												$otherUser = $this->getOtherUserInPrivateConversation($conversation);
											@endphp
											{{ $otherUser ? $otherUser->name : 'Unknown User' }}
										</div>
										@if($conversation->created_by === auth()->id())
											<button
													wire:click.stop="closeConversation({{ $conversation->id }})"
													class="text-gray-400 hover:text-dark-600 dark:hover:text-gray-200">
												<x-icon
														name="x-mark"
														class="size-4" />
											</button>
										@endif
									</div>
								</div>
							@endforeach
						@else
							<div class="text-sm text-gray-500 dark:text-dark-300">No private chats</div>
						@endif
					</div>
				@elseif($activeTab === 'group')
					<div class="py-2">
						<div class="font-medium text-sm text-gray-500 dark:text-dark-300 mb-2">Group Chats</div>
						@php
							$groupConvs = $this->groupConversations();
						@endphp
						@if($groupConvs->count() > 0)
							@foreach($groupConvs as $conversation)
								<div
										wire:click="selectConversation({{ $conversation->id }})"
										class="p-2 rounded-md cursor-pointer hover:bg-gray-100 dark:hover:bg-dark-600 {{ $selectedConversationId === $conversation->id ? 'bg-gray-100 dark:bg-dark-600 border-l-4 border-primary-400 dark:border-primary-500' : '' }}">
									<div class="flex justify-between items-center">
										<div>
											<div class="font-medium">{{ $conversation->name }}</div>
											<div class="text-xs text-gray-500 dark:text-dark-300">
												{{ $this->getActiveUsersCount($conversation) }} participants
											</div>
										</div>
										@if($conversation->created_by === auth()->id())
											<button
													wire:click.stop="closeConversation({{ $conversation->id }})"
													class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
												<x-icon
														name="x-mark"
														class="size-4" />
											</button>
										@endif
									</div>
								</div>
							@endforeach
						@else
							<div class="text-sm text-gray-500 dark:text-dark-300">No group chats</div>
						@endif
					</div>
				@endif
				<div
						x-cloak
						wire:ignore
						class="mt-1 text-sm text-gray-500 dark:text-dark-300 border-t border-gray-200 dark:border-dark-400 pt-2">
					<span class="text-xs">
			<span x-text="Object.keys(members).length"></span>
				Online
			</span>

					<!-- Optional helper so we don't recompute Object.values(members) -->
					<div
							class="flex items-center gap-1"
							x-data="{
        get list(){ return Object.values(members) },
        initials: function(name, email, id){
          var n = (name || '').trim();
          if (n.length) {
            var parts = n.split(/\s+/).filter(Boolean);
            if (parts.length === 1) {
              return parts[0].slice(0, 2).toUpperCase();
            }
            var first = (parts[0] && parts[0][0]) ? parts[0][0] : '';
            var lastPart = parts[parts.length - 1] || '';
            var last = lastPart ? lastPart[0] : '';
            var val = (first + last).toUpperCase();
            return val || '?';
          }
          if (email && typeof email === 'string' && email.length) {
            return email[0].toUpperCase();
          }
          if (id !== undefined && id !== null) {
            return String(id).slice(0, 2).toUpperCase();
          }
          return '?';
        }
      }">

						<template x-for="u in list">
							<!-- ONE root per iteration -->
									<span class="inline-flex items-center gap-1 mt-2">
					        <!-- Avatar -->
					        <img
					        	x-show="u.avatar"
					        	:src="u.avatar"
					        	class="size-6 rounded-full object-cover"
					        	:alt="u.name || ('User #' + (u.id ? u.id : ''))"
					        >
					        <!-- Initials fallback when no avatar -->
					        <span
					        	x-show="!u.avatar"
					        	class="size-6 rounded-full bg-gray-200 dark:bg-dark-600 text-gray-700 dark:text-dark-100 grid place-items-center text-[10px] font-semibold uppercase"
					        	:title="u.name || ('User #' + (u.id ? u.id : ''))"
					        >
 	        				<span x-text="initials(u?.name, u?.email, (typeof u === 'object' ? u?.id : u))"></span>
					        </span>
					      </span>
						</template>
					</div>
				</div>
			</div>

			<!-- Chat area -->
			@php
				$currentConv = $this->currentConversation();
			@endphp
			<div class="flex-1 flex flex-col overflow-hidden {{ $currentConv ? '' : 'items-center justify-center' }}">
				@if($currentConv)
					<!-- Chat header -->
					<div class="p-2 border-b border-gray-200 dark:border-dark-400 flex justify-between items-center">
						<div>
							<h3 class="font-medium">
								@if($currentConv->type === 'public')
									Public Chat
								@elseif($currentConv->type === 'private')
									@php
										$otherUser = $this->getOtherUserInPrivateConversation($currentConv);
									@endphp
									{{ $otherUser ? $otherUser->name : 'Private Chat' }}
								@else
									{{ $currentConv->name }}
								@endif
							</h3>
							@php
								$convUsers = $this->conversationUsers();
							@endphp
							<p class="text-xs text-gray-500 dark:text-dark-300">
								{{ $convUsers->count() }} participants
							</p>
						</div>

						@if($currentConv->created_by === auth()->id() && $currentConv->type !== 'public')
							<button
									wire:click="closeConversation({{ $currentConv->id }})"
									class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
								<x-icon
										name="x-mark"
										class="size-5" />
							</button>
						@endif
					</div>

					<!-- Messages -->
					<div
							id="chatContainer"
							x-data="chatScroller"
							x-init="init()"
							x-ref="list"
							class="flex-1 overflow-y-auto p-2 space-y-4 bg-gray-50 dark:bg-dark-700">
						@php
							$msgs = $this->messages();
						@endphp
						@foreach($msgs as $message)
							<livewire:pages.negotiation.chat.negotiation-chat-message
									:messageId="$message->id"
									:key="$message->id" />

						@endforeach
					</div>

					<!-- Message input -->

					<div
							class="px-3 border-t border-gray-200 dark:border-dark-400"
							x-data="{ selectedButton: null }"
							@click.away="whisperTo = false">
						<div class="py-2 flex items-center gap-2">
							<!-- Urgent Button -->
							<button
									@click="selectedButton = selectedButton === 'isUrgent' ? null : 'isUrgent'; $wire.isUrgent = selectedButton === 'isUrgent'; $wire.isEmergency = false;"
									:class="{ 'text-yellow-500': selectedButton === 'isUrgent', 'text-gray-400': selectedButton !== 'isUrgent' }"
									class="hover:cursor-pointer transition-colors duration-300 ease-in-out">
								<x-icon
										name="exclamation-triangle"
										class="w-4 h-4" />
							</button>

							<!-- Emergency Button -->
							<button
									@click="selectedButton = selectedButton === 'isEmergency' ? null : 'isEmergency'; $wire.isEmergency = selectedButton === 'isEmergency'; $wire.isUrgent = false;"
									:class="{ 'text-red-500': selectedButton === 'isEmergency', 'text-gray-400': selectedButton !== 'isEmergency' }"
									class="hover:cursor-pointer transition-colors duration-300 ease-in-out">
								<x-icon
										name="exclamation-circle"
										class="w-4 h-4" />
							</button>

							<!-- Whisper Button -->
							<button
									@click="selectedButton = selectedButton === 'whisperTo' ? null : 'whisperTo'; $wire.isUrgent = false; $wire.isEmergency = false;"
									:class="{ 'text-indigo-500': selectedButton === 'whisperTo', 'text-gray-400': selectedButton !== 'whisperTo' }"
									class="hover:cursor-pointer transition-colors duration-300 ease-in-out">
								<x-icon
										name="chat-bubble-bottom-center-text"
										class="w-4 h-4" />
							</button>

							<!-- Whisper -->
						</div>
						@if($isWhisper && $whisperToUserId)
							@php
								$whisperUser = $convUsers->firstWhere('id', $whisperToUserId);
							@endphp
							<div class="mb-2 text-xs text-indigo-600 dark:text-indigo-400 flex items-center">
								<span>Whispering to {{ $whisperUser ? $whisperUser->name : 'Unknown User' }}</span>
								<button
										wire:click="toggleWhisper({{ $whisperToUserId }})"
										class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
									<x-icon
											name="x-mark"
											class="size-3" />
								</button>
							</div>
						@endif

						<div class="flex flex-col">
							<!-- Selected Document Display -->
							@if($documentToAttach)
								<div class="mb-2 flex items-center bg-blue-50 dark:bg-blue-900/20 p-2 rounded-md">
									<x-icon
											name="{{ str_ends_with($documentToAttach['file_type'], 'pdf') ? 'document-text' : 'document' }}"
											class="w-4 h-4 mr-2 text-blue-500" />
									<span class="text-sm text-blue-700 dark:text-blue-300 truncate flex-1">{{ $documentToAttach['name'] }}</span>
									<button
											wire:click="clearSelectedDocument"
											class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
										<x-icon
												name="x-mark"
												class="w-4 h-4" />
									</button>
								</div>
							@endif

							<div class="flex items-center">
								<div class="relative flex-1">
									<input
											wire:model="messageInput"
											wire:keydown.enter="sendMessage"
											type="text"
											placeholder="{{ $documentToAttach ? 'Add a message (optional)' : 'Type your message...' }}"
											:class="{
													'bg-yellow-200/20': selectedButton === 'isUrgent',
													'bg-rose-200/20': selectedButton === 'isEmergency',
													'bg-indigo-400/20': selectedButton === 'whisperTo',
													'bg-dark-200 dark:bg-dark-600': !selectedButton
												}"
											x-on:input="onInput($event)"
											class="w-full rounded-md border-0 py-2 pl-3 pr-10 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-dark-400 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6 text-dark-800 dark:text-gray-100">
								</div>
								<x-button
										icon="paper-airplane"
										wire:click="sendMessage"
										class="ml-3 inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
									Send
								</x-button>
							</div>
						</div>

						<!-- Whisper options -->
						@if($convUsers->count() > 1 && ($currentConv->type === 'public' || $currentConv->type === 'group'))
							<div
									x-transition
									x-show="selectedButton === 'whisperTo'"
									class="mt-2">

								<div class="flex items-center justify-between">
									<div class="text-xs text-gray-500 dark:text-dark-300 mb-1">Whisper to:</div>
								</div>

								<div class="flex flex-wrap gap-2">
									@foreach($convUsers->where('id', '!=', auth()->id()) as $user)
										<button
												wire:click="toggleWhisper({{ $user->id }})"
												class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium shadow-sm {{ $isWhisper && $whisperToUserId === $user->id ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-800 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-700' : 'bg-white border border-gray-200 text-gray-700 dark:bg-dark-600 dark:text-gray-200 dark:border-dark-500 hover:bg-gray-50 dark:hover:bg-dark-500' }}">
											{{ $user->name }}
										</button>
									@endforeach
								</div>
							</div>
						@endif
					</div>
				@else
					<div class="text-center p-6">
						<div class="text-gray-500 dark:text-dark-300 mb-4">
							@if($activeTab === 'public')
								Select the public chat to start messaging
							@elseif($activeTab === 'private')
								Select a private chat or create a new one
							@else
								Select a group chat or create a new one
							@endif
						</div>
						<button
								wire:click="openNewConversationModal"
								class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
							<x-icon
									name="plus"
									class="size-4 mr-1" />
							New Conversation
						</button>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- New conversation modal -->
	@if($showNewConversationModal)
		<div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-dark-900 dark:bg-opacity-75 flex items-center justify-center z-50">
			<div class="bg-white dark:bg-dark-700 rounded-lg shadow-xl max-w-md w-full p-6 border border-gray-200 dark:border-dark-600">
				<div class="flex justify-between items-center mb-4">
					<h3 class="text-lg font-medium text-dark-700 dark:text-dark-200">New Conversation</h3>
					<button
							wire:click="closeNewConversationModal"
							class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
						<x-icon
								name="x-mark"
								class="size-5" />
					</button>
				</div>

				<div class="mb-4">
					<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Conversation
					                                                                               Type</label>
					<div class="flex space-x-4">
						<label class="inline-flex items-center">
							<input
									type="radio"
									wire:model.live="newConversationType"
									value="private"
									class="form-radio">
							<span class="ml-2 text-dark-700 dark:text-dark-200">Private</span>
						</label>
						<label class="inline-flex items-center">
							<input
									type="radio"
									wire:model.live="newConversationType"
									value="group"
									class="form-radio">
							<span class="ml-2 text-dark-700 dark:text-dark-200">Group</span>
						</label>
					</div>
				</div>

				@if($newConversationType === 'group')
					<div class="mb-4">
						<label
								for="groupName"
								class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Group
						                                                                                Name</label>
						<input
								wire:model="newConversationName"
								type="text"
								id="groupName"
								class="w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-gray-200 bg-white dark:bg-dark-600 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-dark-400 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
					</div>
				@endif

				<div class="mb-4">
					<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
						{{ $newConversationType === 'private' ? 'Select User' : 'Select Users' }}
					</label>
					<div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-dark-400 rounded-md p-2 bg-gray-50 dark:bg-dark-800 shadow-inner">
						@php
							$availUsers = $this->availableUsers();
						@endphp
						@foreach($availUsers as $user)
							<label class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-dark-600 rounded">
								@if($newConversationType === 'private')
									<input
											type="radio"
											wire:model="selectedUserIds.0"
											value="{{ $user->id }}"
											class="form-radio">
								@else
									<input
											type="checkbox"
											wire:model="selectedUserIds"
											value="{{ $user->id }}"
											class="form-checkbox">
								@endif
								<span class="ml-2 text-dark-700 dark:text-dark-200">{{ $user->name }}</span>
							</label>
						@endforeach
					</div>
				</div>

				<div class="flex justify-end">
					<button
							wire:click="closeNewConversationModal"
							class="mr-3 inline-flex items-center rounded-md bg-white dark:bg-dark-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-dark-400 hover:bg-gray-50 dark:hover:bg-dark-500">
						Cancel
					</button>
					<button
							wire:click="createNewConversation"
							class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
						Create
					</button>
				</div>
			</div>
		</div>
	@endif
</div>

@push('scripts')
	<script>
		window.presenceStore = ({ negotiationId, initialConversationId, currentUserId }) => ({
			negotiationId,
			currentUserId: String(currentUserId ?? ''),

			// channels
			presenceChan: null,
			msgChan: null,

			// state
			currentConversationId: null,
			members: {},       // { [id]: user }
			_lastWhisper: 0,

			init () {
				if (!window.Echo) {
					console.warn('[chat] Echo not initialized')
					return
				}

				// ✅ join presence ONCE for the negotiation
				this.joinPresence()

				// ✅ subscribe to the initially opened conversation (if any)
				if (initialConversationId) {
					this.switchToConversation(null, initialConversationId)
				}

				// listen for Livewire tab/thread changes
				window.addEventListener('conversation-changed', (e) => {
					const { oldId, newId } = e.detail || {}
					if (!newId || newId === this.currentConversationId) return
					this.switchToConversation(oldId, newId)
				})

				// optional: leave presence on page unload
				window.addEventListener('beforeunload', () => {
					try { if (this.presenceChan) window.Echo.leave(`negotiation.${this.negotiationId}`) } catch {}
				})
			},

			joinPresence () {
				const EVENT = '.MessageSent' // use 'MessageSent' if you didn't set broadcastAs('MessageSent') on the PHP event

				this.presenceChan = window.Echo
					.join(`negotiation.${this.negotiationId}`)
					// ✅ roster hooks (restores your online users)
					.here((users) => {
						const map = {}
						users.forEach(u => { map[String(u.id)] = u })
						this.members = map
					})
					.joining((u) => { this.members[String(u.id)] = u })
					.leaving((u) => { delete this.members[String(u.id)] })

					// optional typing whisper
					.listenForWhisper('typing', (payload) => {
						// hook if you want typing indicators
					})

					// ✅ presence also hears MessageSent from ANY conversation in this negotiation
					.listen(EVENT, (payload) => {
						// Livewire is already listening via echo-presence in getListeners(), so you don't
						// have to do anything here. Keep it in case you want console logs while debugging.
						// console.log('[presence] MessageSent', payload);
					})
			},

			switchToConversation (oldId, newId) {
				const EVENT = '.MessageSent'

				// ✅ never touch presence here; only (re)wire the private message stream
				try { if (this.msgChan) this.msgChan.stopListening(EVENT) } catch {}

				// leave previous private channel (optional safety)
				try { if (oldId) window.Echo.leave(`conversation.${oldId}`) } catch {}

				this.msgChan = window.Echo
					.private(`conversation.${newId}`)
					.listen(EVENT, (payload) => {
						// console.log('[private] MessageSent', payload);
					})

				this.currentConversationId = newId

				// tell Livewire to mark read + clear unread
				this.$wire.openConversation(newId)
			},

			// call from your input on keydown to whisper typing
			onInput () {
				if (!this.presenceChan) return
				if (Date.now() - this._lastWhisper < 800) return
				this._lastWhisper = Date.now()
				this.presenceChan.whisper('typing', {
					id: this.currentUserId,
					conversation_id: this.currentConversationId
				})
			},
		})
	</script>

	<script>
		document.addEventListener('livewire:init', () => {
			Livewire.on('notify', (payload = {}) => {
				const title = payload.title || 'New message'
				const message = payload.message || ''
				console.log('[notify]', payload) // prove it fires
				try {
					if ('Notification' in window) {
						if (Notification.permission === 'granted') {
							new Notification(title, { body: message })
						} else if (Notification.permission !== 'denied') {
							Notification.requestPermission().then(p => {
								if (p === 'granted') new Notification(title, { body: message })
							})
						}
					}
				} catch (_) {}

				try {
					if (window?.TallStackUI?.notify) {
						window.TallStackUI.notify({ title, description: message })
						return
					}
				} catch (_) {}
				try {
					if (window?.this.$wireui?.notify) {
						window.this.$wireui.notify({
							title,
							description: message,
							icon: 'chat-bubble-left-right'
						})
						return
					}
				} catch (_) {}

				const toast = document.createElement('div')
				toast.textContent = `${title}: ${message}`
				Object.assign(toast.style, {
					position: 'fixed', right: '1rem', bottom: '1rem',
					background: '#111', color: '#fff', padding: '0.75rem 1rem',
					borderRadius: '0.5rem', zIndex: 9999, boxShadow: '0 8px 24px rgba(0,0,0,.35)',
					maxWidth: '24rem', lineHeight: '1.25rem', fontSize: '0.875rem',
				})
				document.body.appendChild(toast)
				setTimeout(() => toast.remove(), 3500)
			})
		})
	</script>

	<script>
		document.addEventListener('alpine:init', () => {
			Alpine.data('chatScroller', () => ({
				// refs
				listEl: null,
				// internals
				_observer: null,
				_afterRender (fn) { requestAnimationFrame(() => this.$nextTick(fn)) },

				init () {
					this.listEl = this.$refs.list
					if (!this.listEl) return

					// Initial snap to bottom on mount
					this._afterRender(() => this.scrollToBottom())

					// Auto-scroll when DOM children change (new messages arrive)
					this._observer = new MutationObserver(() => {
						// Only autoscroll if user is already near bottom
						if (this.isNearBottom()) {
							this._afterRender(() => this.scrollToBottom())
						}
					})
					this._observer.observe(this.listEl, { childList: true, subtree: true })

					// Listen for explicit Livewire events (see parts B & C below)
					window.addEventListener('new-message', () => {
						this._afterRender(() => this.scrollToBottom())
					})
					window.addEventListener('conversation-changed', () => {
						this._afterRender(() => this.scrollToBottom())
					})

					// Optional: re-scroll when images load (if you ever show images)
					this.listEl.addEventListener('load', (e) => {
						if (e.target.tagName === 'IMG') this._afterRender(() => this.scrollToBottom())
					}, true)
				},

				isNearBottom (threshold = 80) {
					if (!this.listEl) return false
					const { scrollTop, scrollHeight, clientHeight } = this.listEl
					return (scrollHeight - (scrollTop + clientHeight)) < threshold
				},

				scrollToBottom () {
					if (!this.listEl) return
					this.listEl.scrollTop = this.listEl.scrollHeight
				},

				// Clean up when Alpine component is destroyed
				destroy () {
					this._observer?.disconnect()
				},
			}))
		})
	</script>

@endpush





