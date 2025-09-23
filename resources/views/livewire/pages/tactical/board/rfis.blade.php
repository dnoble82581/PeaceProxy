<?php

	use Carbon\Carbon;
	use Livewire\WithFileUploads;

	new class extends \Livewire\Volt\Component {
		use WithFileUploads;

		public int $negotiationId;
		public \App\Models\Negotiation $negotiation;

		// View/respond state
		public ?int $viewingRfiId = null;
		public ?\App\Models\RequestForInformation $viewingRfi = null;
		public $replies = [];
		public string $replyBody = '';
		public bool $showResponsesModal = false;

		// Document attachments state
		public $docFile = null;
		public string $docName = '';
		public ?string $docCategory = null;
		public ?string $docDescription = null;
		public bool $docPrivate = false;
		public bool $showUploadDocModal = false;
		public bool $showViewDocModal = false;
		public $currentDocument = null;
		public $documentUrl = null;

		public function mount(int $negotiationId)
		{
			$this->negotiationId = $negotiationId;
			$this->reloadNegotiation();
		}

		private function reloadNegotiation():void
		{
			$this->negotiation = app(\App\Services\Negotiation\NegotiationFetchingService::class)
				->getNegotiationById($this->negotiationId, ['rfis', 'rfis.replies', 'rfis.sender']);
		}

		public function openResponsesModal(int $rfiId):void
		{
			$this->viewingRfiId = $rfiId;
			$this->viewingRfi = app(\App\Services\RequestForInformation\RequestForInformationFetchingService::class)->getRfiById($rfiId);

			// Ensure documents relation is available
			if ($this->viewingRfi) {
				$this->viewingRfi->load('documents');
			}

			$this->loadReplies();
			$this->showResponsesModal = true;

			// Mark recipient and replies as read for current user if applicable
			$recipient = app(\App\Services\RequestForInformationRecipient\RequestForInformationRecipientFetchingService::class)
				->getRecipientByRfiIdAndUserId($rfiId, auth()->id());
			if ($recipient && !$recipient->is_read) {
				app(\App\Services\RequestForInformationRecipient\RequestForInformationRecipientUpdateService::class)
					->updateReadStatus($recipient->id, true);
			}

			if ($recipient && $this->viewingRfi) {
				$unread = $this->viewingRfi->replies()->where('is_read', false)->get();
				foreach ($unread as $reply) {
					$reply->is_read = true;
					$reply->save();
				}
			}
		}

		public function loadReplies():void
		{
			if ($this->viewingRfiId) {
				$this->replies = app(\App\Services\RequestForInformationReply\RequestForInformationReplyFetchingService::class)
					->getRepliesByRfiId($this->viewingRfiId);
				$this->replyBody = '';
			}
		}

		public function submitReply():void
		{
			$this->validate([
				'replyBody' => 'required|string',
			]);

			$dto = new \App\DTOs\RequestForInformationReply\RequestForInformationReplyDTO(
				null,
				tenant()->id,
				$this->viewingRfiId,
				auth()->id(),
				$this->replyBody,
				false,
				Carbon::now(),
				Carbon::now(),
				null
			);

			app(\App\Services\RequestForInformationReply\RequestForInformationReplyCreationService::class)
				->createReply($dto, $this->negotiationId);

			$this->replyBody = '';
			$this->loadReplies();
			$this->reloadNegotiation();
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
				'tenant_id' => tenant()->id,
			];

			app(\App\Services\Document\DocumentStorageService::class)->createRfiDocument($data, $this->viewingRfiId,
				$this->docFile);

			$this->resetDocForm();
			$this->showUploadDocModal = false;

			// Refresh the RFI documents list
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
			// Reset viewer state if we deleted the open doc
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

		public function handleRfiCreated(array $event):void
		{
			$this->reloadNegotiation();
		}

		public function handleReplyPosted(array $event):void
		{
			// If the posted reply belongs to the RFI we're viewing, refresh replies
			if (!empty($this->viewingRfiId) && ($event['request_for_information_id'] ?? null) === $this->viewingRfiId) {
				$this->loadReplies();
			}
			// Always refresh counts in the table
			$this->reloadNegotiation();
		}

		#[\Livewire\Attributes\On('close-modal')]
		public function closeModal():void
		{
			$this->showResponsesModal = false;
		}
	}

?>

<div>
	<div class="px-4 sm:px-6 lg:px-8">
		<div class="flow-root">
			<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
				<div class="inline-block min-w-full py-1">
					<div class="overflow-hidden shadow-sm outline-1 outline-black/5 sm:rounded-lg dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
						<table class="relative min-w-full divide-y divide-gray-300 dark:divide-white/15">
							<thead class="bg-dark-50 dark:bg-dark-800/75">
							<tr>
								<th
										scope="col"
										class="py-2 pr-3 pl-4 text-left text-xs font-semibold text-dark-900 sm:pl-6 dark:text-gray-200">
									Title
								</th>
								<th
										scope="col"
										class="px-3 py-2 text-left text-xs font-semibold text-dark-900 dark:text-gray-200">
									Status
								</th>
								<th
										scope="col"
										class="px-3 py-2 text-left text-xs font-semibold text-dark-900 dark:text-gray-200">
									Replies
								</th>
								<th
										scope="col"
										class="px-3 py-2 text-left text-xs font-semibold text-dark-900 dark:text-gray-200">
									Created
								</th>
								<th
										scope="col"
										class="py-3.5 pr-4 pl-3 sm:pr-6"><span class="sr-only">View</span></th>
							</tr>
							</thead>
							<tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-dark-800/50">
							@forelse($negotiation->rfis as $rfi)
								<tr>
									<td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-dark-900 sm:pl-6 dark:text-white">{{ $rfi->title }}</td>
									<td class="px-3 py-4 text-sm whitespace-nowrap text-dark-500 dark:text-dark-400">
										<x-badge :text="$rfi->status" />
									</td>
									<td class="px-3 py-4 text-sm whitespace-nowrap text-dark-500 dark:text-dark-400">{{ $rfi->replies->count() }}</td>
									<td class="px-3 py-4 text-sm whitespace-nowrap text-dark-500 dark:text-dark-400">
										@php
											$formattedDatetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', (string) $rfi->created_at, 'UTC')
												->setTimezone(authUser()->timezone)
												->format('Y-m-d H:i');
										@endphp
										{{ $formattedDatetime }}
									</td>
									<td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
										<x-button.circle
												wire:click="openResponsesModal({{ $rfi->id }})"
												color="emerald"
												icon="eye"
												sm />
									</td>
								</tr>
							@empty
								<tr>
									<td
											colspan="5"
											class="px-3 py-4 text-sm text-dark-500 dark:text-dark-400">No RFIs found for
									                                                                   this negotiation.
									</td>
								</tr>
							@endforelse
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- View RFI & Responses Modal -->
	<x-modal
			id="view-rfi-responses-modal"
			size="6xl"
			wire="showResponsesModal"
			x-on:hidden.window="$wire.closeModal()">
		<x-card title="Request Details & Responses">
			<div class="space-y-6">
				@if($viewingRfi)
					<div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4 border border-gray-200 dark:border-dark-600">
						<h3 class="text-lg font-semibold mb-2">{{ $viewingRfi->title }}</h3>
						<div class="flex items-center mb-3">
							<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">{{ $viewingRfi->status }}</span>
							<span class="text-xs text-gray-500 dark:text-dark-300 ml-3">Created {{ $viewingRfi->created_at?->format('Y-m-d H:i') }}</span>
						</div>
						<div class="prose dark:prose-invert max-w-none">
							<p>{{ $viewingRfi->body }}</p>
						</div>
						<div class="mt-3 text-sm text-gray-500 dark:text-dark-300">
							<p>From: {{ $viewingRfi->sender?->name }}</p>
						</div>
					</div>
				@endif

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
											color="sky"
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
											<p class="font-semibold text-gray-800 dark:text-dark-100">{{ is_string($reply->user->name ?? null) ? $reply->user->name : '' }}</p>
											<p class="text-sm text-gray-500 dark:text-dark-300">{{ $reply->created_at ? $reply->created_at->format('Y-m-d H:i') : '' }}</p>
										</div>
									</div>
									<div class="mt-2">
										<p class="text-gray-700 dark:text-dark-200">{{ is_string($reply->body ?? null) ? $reply->body : '' }}</p>
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
							id="rfi-doc-file"
							class="mt-1 block w-full" />
				</div>
				<div>
					<x-input
							label="Name"
							wire:model="docName"
							id="rfi-doc-name"
							class="mt-1 block w-full" />
				</div>
				<div>
					<x-input
							label="Category"
							wire:model="docCategory"
							id="rfi-doc-category"
							class="mt-1 block w-full" />
				</div>
				<div>
					<x-textarea
							label="Description"
							wire:model="docDescription"
							id="rfi-doc-description"
							class="mt-1 block w-full" />
				</div>
				<div class="flex items-center">
					<x-checkbox
							label="Private"
							wire:model="docPrivate"
							id="rfi-doc-private" />
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
