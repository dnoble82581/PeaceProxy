<?php
/**
 * Negotiation Chat Component
 *
 * This Livewire component provides a real-time chat interface for negotiations, supporting:
 * - Public chat rooms for all participants
 * - Private one-on-one conversations
 * - Group conversations with multiple participants
 * - Whisper functionality for private messages within a conversation
 * - Real-time presence indicators showing online users
 * - Unread message notifications
 *
 * The component uses Laravel Echo for real-time communication and Alpine.js for frontend interactivity.
 */

	use App\DTOs\Conversation\ConversationDTO;
	use App\DTOs\Message\MessageDTO;
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

		/**
		 * The ID of the current negotiation
		 *
		 * @var int
		 */
		public int $negotiationId;

		/**
		 * Version counter for the conversation list
		 * Incremented to trigger UI updates when conversations change
		 *
		 * @var int
		 */
		public int $listVersion = 0;

		/**
		 * Array of unread message counts indexed by conversation ID
		 *
		 * @var array
		 */
		public array $unread = [];

		/**
		 * The message input text
		 *
		 * @var string
		 */
		public string $messageInput = '';

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

		/**
		 * The ID of the previously selected conversation
		 * Used for tracking conversation changes
		 *
		 * @var int|null
		 */
		public ?int $previousConversationId = null;

		/**
		 * Initialize the component
		 *
		 * Ensures the public conversation exists and sets it as the selected conversation
		 *
		 * @param int $negotiationId The ID of the negotiation
		 * @return void
		 */
		public function mount($negotiationId):void
		{

			$this->unread = app(ConversationReadService::class)
				->seedUnreadForNegotiation(auth()->user(), $this->negotiationId);

			if ($this->selectedConversationId) {
				$this->unread[$this->selectedConversationId] = 0;
				// optionally persist
				app(ConversationReadService::class)
					->markConversationRead(auth()->user(), $this->selectedConversationId);
			}

			// Create public conversation if it doesn't exist using the service
			$conversationCreationService = app(ConversationCreationService::class);
			$publicConversation = $conversationCreationService->ensurePublicConversation(
				auth()->user()->tenant_id,
				auth()->id(),
				$this->negotiationId
			);

			$this->selectedConversationId = $publicConversation->id;
			$this->negotiationId = $negotiationId;
		}

		/**
		 * Lifecycle hook called before updating the selected conversation ID
		 * Stores the previous conversation ID for reference
		 *
		 * @param int $value The new conversation ID
		 * @return void
		 */
		public function updatingSelectedConversationId(int $value):void
		{
			$this->previousConversationId = $this->selectedConversationId;
		}

		/**
		 * Lifecycle hook called after updating the selected conversation ID
		 * Dispatches an event to notify Alpine.js to switch presence rooms
		 *
		 * @param int $value The new conversation ID
		 * @return void
		 */
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
			if (empty(trim($this->messageInput)) || !$this->currentConversation()) {
				return;
			}

			$messageData = [
				'conversation_id' => $this->currentConversation()->id,
				'negotiation_id' => $this->negotiationId,
				'tenant_id' => tenant()->id,
				'user_id' => auth()->id(),
				'content' => $this->messageInput,
				'is_whisper' => $this->isWhisper,
			];

			if ($this->isWhisper && $this->whisperToUserId) {
				$messageData['whisper_to'] = $this->whisperToUserId;
			}

			$messageDTO = MessageDTO::fromArray($messageData);
			app(MessageCreationService::class)->createMessage($messageDTO);

			$this->reset('messageInput', 'isWhisper', 'whisperToUserId');

			// Dispatch event to trigger scroll
			$this->dispatch('new-message');
		}

		/**
		 * Select a conversation by ID
		 *
		 * @param  int  $conversationId  The ID of the conversation to select
		 * @return void
		 */
		public function selectConversation(int $conversationId):void
		{
			$this->selectedConversationId = $conversationId;
		}

		/**
		 * Set the active tab and select the appropriate conversation
		 * 
		 * If the public tab is selected, automatically selects the public conversation.
		 * For other tabs, clears the selected conversation.
		 *
		 * @param  string  $tab  The tab to set as active (public, private, or group)
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
		 * If the user is already selected for whispering, turns off whisper mode.
		 * Otherwise, enables whisper mode and sets the selected user.
		 *
		 * @param  int  $userId  The ID of the user to whisper to
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
		 * Validates the input based on conversation type:
		 * - Private conversations must have exactly one other user
		 * - Group conversations must have at least one other user and a name
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
		 * If the closed conversation is currently selected, clears the selection.
		 *
		 * @param  int  $conversationId  The ID of the conversation to close
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
		 * Sets up listeners for:
		 * - Negotiation-wide presence channel events (ConversationCreated, ConversationClosed, MessageSent)
		 * - Conversation-specific private channel events (MessageSent)
		 *
		 * @return array The event listeners
		 */
		public function getListeners():array
		{
			$listeners = [
				// negotiation-wide presence channel (singular)
				"echo-presence:negotiation.{$this->negotiationId},.ConversationCreated" => 'handleConversationCreated',
				"echo-presence:negotiation.{$this->negotiationId},.ConversationClosed" => 'handleConversationCreated',

				// optional but recommended for list badges/snippets
				"echo-presence:negotiation.{$this->negotiationId},.MessageSent" => 'handleMessageSent',
			];

			// live stream for the currently open thread
			if ($this->selectedConversationId) {
				$listeners["echo-private:conversation.{$this->selectedConversationId},.MessageSent"] = 'handleMessageSent';
			}

			return $listeners;
		}

		/**
		 * Handle a message sent event
		 * 
		 * Updates the UI based on whether the message is for the current conversation or another conversation:
		 * - For current conversation: Clears unread count and triggers UI updates
		 * - For other conversations: Increments unread count and shows notification
		 *
		 * @param  array  $data  The event data containing conversation_id, message_id, and sender_id
		 * @return void
		 */
		public function handleMessageSent(array $data):void
		{
			$conversationId = (int) ($data['conversation_id'] ?? 0);
			$messageId = (int) ($data['message_id'] ?? 0);
			$senderId = (int) ($data['sender_id'] ?? 0);

			if (!$conversationId) {
				return;
			}

			// --- De-dupe: presence + private will both fire for the open thread ---
			static $seen = [];
			if ($messageId && isset($seen[$messageId])) {
				return;
			}
			if ($messageId) {
				$seen[$messageId] = true;
				if (count($seen) > 500) {
					array_shift($seen);
				} // simple prune
			}

			// --- Always bump listVersion so last-message snippet/badges refresh ---
			$this->listVersion++;

			// --- If the message is for the currently open thread ---
			if ($conversationId === (int) $this->selectedConversationId) {

				// If it wasn't sent by me, clear unread + persist read position
				if ($senderId !== (int) auth()->id()) {
					// local badge reset (assumes you added public array $unread = [];)
					$this->unread[$conversationId] = 0;

					// optional but recommended: persist read markers
					app(ConversationReadService::class)
						->markConversationRead(auth()->user(), $conversationId);
				}

				// UI hooks you already use to keep the viewport pinned, etc.
				$this->dispatch('new-message');
				$this->dispatch('message-received');
				return;
			}

			// --- Message is for some other conversation ---
			// Don't count my own outbound echo as "new"
			if ($senderId !== (int) auth()->id()) {
				$this->unread[$conversationId] = ($this->unread[$conversationId] ?? 0) + 1;

				// Optional toast
				$this->dispatch('notify', [
					'title' => 'New Message',
					'message' => 'You have a new message in '.($data['conversation_name'] ?? 'a conversation'),
				]);
			}
		}

		/**
		 * Handle a conversation created event
		 * 
		 * Updates the conversation lists and UI when a new conversation is created
		 *
		 * @param  array  $data  The event data containing conversation_id
		 * @return void
		 */
		public function handleConversationCreated(array $data):void
		{
			// Refresh the conversation lists
			// $this->privateConversations();
			// $this->groupConversations();

			$this->listVersion++;

			// Optionally set initial unread=0 for the new conversation
			if ($id = (int) ($data['conversation_id'] ?? 0)) {
				$this->unread[$id] = 0;

				// Force a re-render to ensure the UI is updated
				$this->dispatch('conversation-list-updated');
			}
		}
	}
?>