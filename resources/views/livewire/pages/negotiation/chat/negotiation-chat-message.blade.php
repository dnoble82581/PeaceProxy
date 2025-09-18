<?php

	use App\DTOs\MessageReaction\MessageReactionDTO;
	use App\Models\Message;
	use App\Models\MessageReaction;
	use App\Services\Message\MessageReactionService;
	use Illuminate\Support\Collection;

	new class extends \Livewire\Volt\Component {
		public Message $message;
		public string $formattedMessage;
		public bool $isUrgent = false;
		public bool $isEmergency = false;
		public bool $isWhisper;
		public int $negotiationId;


		public function mount(int $messageId)
		{
			$this->message = $this->fetchMessage($messageId);
			$this->negotiationId = $this->message->negotiation_id;
			$this->formattedMessage = $this->formatMessage($this->message);
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
//			dd($decryptedMessage, $this->isUrgent, $this->isEmergency, $this->isUrgent);

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

		public function getListeners():array
		{
			$userId = authUser()->id;
			// This component only needs to listen for events related to its specific message
			// The parent component (negotiation-chat) handles conversation-level events
			return [
				// Listen for reaction updates for this specific message
				"reaction-updated-{$this->message->id}" => '$refresh',
				"echo-private:private.users.$userId,.MessageReactionChanged" => 'handleReactionChanged',
			];
		}

		public function handleReactionChanged()
		{
			logger('reaction changed');
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

				<!-- Document Attachments -->
				@php
					$attachedDocuments = $message->messageDocuments()->with('document')->get();
				@endphp

				@if($attachedDocuments->count() > 0)
					<div class="mt-2 pt-2 border-t border-gray-200 dark:border-dark-500">
						@foreach($attachedDocuments as $attachment)
							<a
									href="{{ route('documents.download', $attachment->document_id) }}"
									class="flex items-center p-1 rounded hover:bg-gray-100 dark:hover:bg-dark-500 text-blue-600 dark:text-blue-400"
									target="_blank">
								<x-icon
										name="{{ str_ends_with($attachment->document->file_type, 'pdf') ? 'document-text' : 'document' }}"
										class="w-4 h-4 mr-2" />
								<span class="text-xs truncate">{{ $attachment->document->name }}</span>
								<x-icon
										name="arrow-down-tray"
										class="w-3 h-3 ml-1" />
							</a>
						@endforeach
					</div>
				@endif

				<!-- Message Reactions -->

				<div class="mt-1 flex gap-2 text-xs text-gray-500">
					@foreach (['👍','👎','❤️','😂','😮'] as $e)
						{{-- @php($count = $this->reactionCount($message->id, $e)) --}}
						{{-- @if($count > 0) --}}
						{{-- <span wire:key="rc-{{ $message->id }}-{{ $e }}">{{ $e }} 0</span> --}}
						{{-- @endif --}}
					@endforeach
				</div>
			</div>
			<div
					wire:ignore
					x-data="{
							messageId: {{ $message->id }},
							active: @js($this->userReactionType($message->id)), // '❤️' | null
							emojis: ['👍','👎','❤️','😂','😮'],
							set(e) {
								this.active = (this.active === e) ? null : e; // toggle same emoji clears
								$wire.setReaction(this.messageId, this.active);
							}
						}"
					@reaction-updated-{{ $message->id }}.window="if ($event.detail && 'active' in $event.detail) active = $event.detail.active;"
			>
				<div class="flex items-center gap-1 select-none">
					<template
							x-for="emoji in emojis"
							:key="emoji">
						<button
								type="button"
								x-on:click="set(emoji)"
								:data-active="active === emoji ? 'true' : 'false'"
								:aria-pressed="active === emoji ? 'true' : 'false'"
								class="text-xs p-1 rounded-full transition hover:bg-gray-100 dark:hover:bg-neutral-800 data-[active=true]:bg-gray-100 data-[active=true]:dark:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-offset-1"
								x-text="emoji"
						></button>
					</template>
				</div>
			</div>
		</div>
	</div>
</div>
