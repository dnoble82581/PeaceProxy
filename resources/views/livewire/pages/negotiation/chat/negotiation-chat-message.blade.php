<?php

	use App\DTOs\MessageReaction\MessageReactionDTO;
	use App\Models\Message;
	use App\Models\MessageReaction;
	use App\Services\Message\MessageReactionService;
	use Illuminate\Support\Collection;
	use Livewire\Attributes\On;

	new class extends \Livewire\Volt\Component {
		public Message $message;
		public string $formattedMessage;
		public bool $isUrgent = false;
		public bool $isEmergency = false;
		public bool $isWhisper;
		public int $negotiationId;
		public array $reactions = [];

		public function mount(int $messageId)
		{
			$this->message = $this->fetchMessage($messageId);
			$this->negotiationId = $this->message->negotiation_id;
			$this->formattedMessage = $this->formatMessage($this->message);
			$this->reactions = app(MessageReactionService::class)->getMessageReactionCounts($messageId);
		}

		public function fetchMessage(int $messageId):Message
		{
			return app(\App\Services\Message\MessageFetchingService::class)->getMessage($messageId, ['user']);
		}

		public function formatMessage(Message $message):string
		{
			$decryptedMessage = decrypt($message->content);
			$this->isUrgent = str_starts_with($decryptedMessage, '[URGENT] ');
			$this->isEmergency = str_starts_with($decryptedMessage, '[EMERGENCY] ');
			$this->isWhisper = $message->is_whisper;
			// dd($decryptedMessage, $this->isUrgent, $this->isEmergency, $this->isUrgent);

			if ($this->isUrgent) {
				return substr($decryptedMessage, 9); // Remove [URGENT] prefix
			} elseif ($this->isEmergency) {
				return substr($decryptedMessage, 12); // Remove [EMERGENCY] prefix
			} else {
				return $decryptedMessage;
			}
		}

		public function setReaction(int $messageId, ?string $emoji):void
		{
			$result = app(MessageReactionService::class)
				->setReaction($messageId, auth()->id(), $emoji);

			// keep existing listeners working
			$this->dispatch('reaction-updated-'.$messageId, active: $result['emoji']);
		}

		public function userReactionType(int $messageId):?string
		{
			if (!authUser()->id) {
				return null;
			}

			return app(MessageReactionService::class)->getUserReactionType($messageId);
		}

		/**
		 * Get reactions for a message grouped by type
		 */
		public function getMessageReactions(int $messageId):array
		{
			return app(MessageReactionService::class)->getReactionsGroupedByType($messageId);
		}

		public function getMessageReactionCounts()
		{
			return app(MessageReactionService::class)->getMessageReactionCounts($this->message->id);
		}

		public function getMessageReactionCount($emoji)
		{
			return app(MessageReactionService::class)->getMessageReactionCount($this->message->id, $emoji);
		}

		#[On('attachDocumentToMessage')]
		public function attachDocumentToMessage()
		{
			dd('here');
		}

		public function getListeners():array
		{
			$userId = authUser()->id;
			$tenantId = tenant()->id;
			$negotiationId = $this->negotiationId;

			return [
				// Listen for reaction updates on user's private channel
				"echo-private:private.users.$userId,.MessageReactionChanged" => 'handleReactionChanged',
				// Listen for reaction updates on the negotiation channel
				"echo-private:private.negotiation.$tenantId.$negotiationId,.MessageReactionChanged" => 'handleReactionChanged',
			];
		}

		public function handleReactionChanged($payload):void
		{
			// Ensure this is for THIS message
			if (($payload['message_id'] ?? null) !== $this->message->id) return;

			// Re-emit as a DOM event so Alpine updates counts immediately
			$this->dispatch("reaction-updated-{$this->message->id}",
				newEmoji: $payload['newEmoji'] ?? ($payload['emoji'] ?? null),
				newEmojiCount: $payload['newEmojiCount'] ?? ($payload['count'] ?? null),
				oldEmoji: $payload['oldEmoji'] ?? null,
				oldEmojiCount: $payload['oldEmojiCount'] ?? null,
				reactor: $payload['reactor'] ?? null,
			);
		}
	}
?>

<div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
	<div class="flex items-center max-w-xs md:max-w-md space-x-2 {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
		<div class="flex-shrink-0">
			<x-avatar
					image="{{ $message->user->avatarUrl() }}"
					sm />
		</div>
		<div>
			<div class="flex items-end space-x-1">
				<div class="font-medium dark:text-white flex items-end text-dark-800 text-xs {{ $message->user_id === auth()->id() ? 'text-right' : '' }}">
					<span>{{ $message->user->name }}</span>
					@if($message->is_whisper)
						<span class="text-primary-500 dark:text-primary-400 text-xs">
							whispered to {{ $message->whisperRecipient->name }}
						</span>
					@endif
					@if($isUrgent)
						<span class="text-yellow-500 dark:text-yellow-400 text-xs ml-1">
							<x-icon
									name="exclamation-triangle"
									class="w-3 h-3 inline" />
						</span>
					@endif
					@if($isEmergency)
						<span class="text-red-500 dark:text-red-400 text-xs ml-1">
							<x-icon
									name="exclamation-circle"
									class="w-3 h-3 inline" />
						</span>
					@endif
				</div>
				<div class="text-xs text-gray-500 dark:text-dark-300">
					{{ $message->created_at->setTimezone(auth()->user()->timezone)->format('g:i A') }}
				</div>
			</div>

			<div
					class="mt-1 rounded-lg px-3 py-1 text-sm shadow-sm
				{{ $isUrgent ? 'bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200' :
				($isEmergency ? 'bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200' :
				($message->is_whisper ? ($message->user_id === auth()->id() ? 'bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200' : 'bg-indigo-200 dark:bg-indigo-700 text-indigo-800 dark:text-indigo-200') :
				($message->user_id === auth()->id() ? 'bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200' : 'bg-white dark:bg-dark-600 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-dark-500'))) }}">
				{{ $formattedMessage }}
			</div>
			<div
					wire:ignore
					x-data="{
    messageId: {{ $message->id }},
    userId: @js(auth()->id()),
    emojis: ['👍','👎','❤️','😂','😮'],
    counts: @js($this->getMessageReactionCounts($message->id)), // { '👍': 3, '😂': 1, ... }
    active: @js($this->userReactionType($message->id)),

    handleReactionChanged(e) {
      const d = e.detail || {};
      
      // Track the last processed event to avoid duplicate processing
      if (this._lastEventId && this._lastEventId === `${d.message_id}-${d.reactor?.id}-${d.newEmoji}`) {
        return; // Skip duplicate events
      }
      
      // Store this event's unique identifier
      this._lastEventId = `${d.message_id}-${d.reactor?.id}-${d.newEmoji}`;
      
      // Update counts from authoritative server data
      if (d.newEmoji != null) this.counts[d.newEmoji] = d.newEmojiCount ?? 0;
      if (d.oldEmoji != null) this.counts[d.oldEmoji] = d.oldEmojiCount ?? 0;
      
      // Only update active state if this event is about the current user
      if (d.reactor?.id === this.userId) {
        this.active = d.newEmoji ?? null;
      }
    },

    set(emoji) {
      const next = (this.active === emoji) ? null : emoji;
      
      // Reset the last event ID to ensure we process the upcoming server response
      this._lastEventId = null;
      
      // Don't update counts locally - let the server provide the authoritative count
      // Just update the active state for immediate UI feedback
      this.active = next;

      $wire.setReaction(this.messageId, this.active);
    }
  }"
					{{-- IMPORTANT: use the *same* event name, with the id interpolated in Blade --}}
					@reaction-updated-{{ $message->id }}.window="handleReactionChanged($event)"
			>
				<div
						class="flex items-center gap-1 select-none mt-1">

					<template
							x-for="emoji in emojis"
							:key="emoji">
						<button
								type="button"
								x-on:click="set(emoji)"
								:data-active="active === emoji ? 'true' : 'false'"
								:aria-pressed="active === emoji ? 'true' : 'false'"
								class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full transition hover:bg-gray-100 dark:hover:bg-neutral-800 data-[active=true]:bg-gray-100 data-[active=true]:dark:bg-neutral-800"
						>
							<span x-text="emoji"></span>
							<span
									class="tabular-nums"
									x-text="counts[emoji] ?? 0"></span>
						</button>
					</template>
				</div>
			</div>
		</div>
	</div>
</div>
