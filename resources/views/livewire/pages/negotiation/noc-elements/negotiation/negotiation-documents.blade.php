<?php

	use App\Livewire\Forms\CreateDocumentForm;
	use App\Models\Negotiation;
	use App\Services\Document\DocumentDestructionService;
	use App\Services\Document\DocumentFetchingService;
	use App\Services\Document\DocumentStorageService;
	use Illuminate\Support\Collection;
	use Livewire\Attributes\Layout;
	use Livewire\WithFileUploads;
	use Livewire\Volt\Component;

	new class extends Component {
		use WithFileUploads;

		public Negotiation $negotiation;
		public CreateDocumentForm $form;
		public $file;
		public $showUploadModal = false;
		public $showViewModal = false;
		public $currentDocument = null;
		public $documentUrl = null;

		public function mount($negotiationId)
		{
			$this->negotiation = $this->fetchNegotiation($negotiationId);
			$this->form->tenant_id = auth()->user()->tenant_id;
			$this->form->documentable_type = 'App\\Models\\Negotiation';
			$this->form->documentable_id = $this->negotiation->id;
		}

		private function fetchNegotiation($negotiationId)
		{
			return Negotiation::query()
				->with([
					'documents' => function ($query) {
						$query->select('id', 'documentable_id', 'documentable_type', 'name', 'file_type', 'file_size',
							'category', 'description', 'is_private', 'uploaded_by_id', 'created_at');
					}
				])
				->select('id', 'title')
				->findOrFail($negotiationId);
		}

		public function deleteDocument($documentId):void
		{
			app(DocumentDestructionService::class)->deleteDocument($documentId);
			$this->negotiation = $this->fetchNegotiation($this->negotiation->id);
		}

		public function uploadDocument():void
		{
			$this->validate([
				'file' => 'required|file|max:10240', // 10MB max
				'form.name' => 'required|string|max:255',
				'form.category' => 'nullable|string|max:255',
				'form.description' => 'nullable|string',
				'form.is_private' => 'boolean',
			]);

			$data = $this->form->all();
			$data['uploaded_by_id'] = auth()->id();
			$data['negotiation_id'] = $this->negotiation->id;

			app(DocumentStorageService::class)->createNegotiationDocument($data, $this->negotiation->id, $this->file);

			$this->reset('file');
			$this->form->reset();
			$this->form->tenant_id = auth()->user()->tenant_id;
			$this->form->documentable_type = 'App\\Models\\Negotiation';
			$this->form->documentable_id = $this->negotiation->id;
			$this->showUploadModal = false;

			$this->negotiation = $this->fetchNegotiation($this->negotiation->id);
		}

		public function viewDocument($documentId):void
		{
			$this->currentDocument = app(DocumentFetchingService::class)->getDocumentById($documentId);
			$this->documentUrl = app(DocumentFetchingService::class)->getDocumentPresignedUrl($documentId, 300);
			$this->showViewModal = true;
		}

		public function resetForm():void
		{
			$this->reset('file');
			$this->form->reset();
			$this->form->tenant_id = auth()->user()->tenant_id;
			$this->form->documentable_type = 'App\\Models\\Negotiation';
			$this->form->documentable_id = $this->negotiation->id;
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
	}

?>

<div>
	<div class="mt-2 flow-root overflow-hidden rounded-t-lg">
		<div class="">
			<table class="w-full text-left">
				<thead class="dark:bg-dark-600">
				<tr>
					<th
							scope="col"
							class="relative isolate px-3 text-left text-xs font-semibold text-primary-950 dark:text-dark-100">
						Name
						<div class="absolute inset-y-0 right-full -z-10 w-screen border-b border-b-gray-200"></div>
						<div class="absolute inset-y-0 left-0 -z-10 w-screen border-b border-b-gray-200"></div>
					</th>
					<th
							scope="col"
							class="hidden px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100 sm:table-cell">
						Type
					</th>
					<th
							scope="col"
							class="hidden px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100 md:table-cell">
						Size
					</th>
					<th
							scope="col"
							class="px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100">
						Category
					</th>
					<th
							scope="col"
							class="py-2 pl-3">
						<span class="sr-only">Actions</span>
					</th>
					<th
							scope="col"
							class="relative">
						<div>
							<x-button.circle
									color=""
									wire:click="$set('showUploadModal', true)"
									sm
									flat
									icon="plus"
									type="button">
							</x-button.circle>
						</div>
					</th>
				</tr>
				</thead>
				<tbody>
				@foreach($this->negotiation->documents as $document)
					<tr>
						<td class="relative pl-3 text-xs font-medium text-primary-950 dark:text-dark-100">
							{{ $document->name }}
							<div class="absolute right-full bottom-0 h-px w-screen bg-gray-100"></div>
							<div class="absolute bottom-0 left-0 h-px w-screen bg-gray-100"></div>
						</td>
						<td class="hidden px-3 py-2 text-xs dark:text-dark-400 text-gray-500 sm:table-cell">
							{{ $document->file_type }}
						</td>
						<td class="hidden px-3 py-2 text-xs dark:text-dark-400 text-gray-500 md:table-cell">
							{{ $this->formatFileSize($document->file_size) }}
						</td>
						<td class="px-3 py-2 text-xs dark:text-dark-400 text-gray-500">
							{{ $document->category }}
						</td>
						<td class="text-right">
							<x-button.circle
									wire:click="viewDocument({{ $document->id }})"
									flat
									color="sky"
									icon="eye"
									sm />
							<x-button.circle
									wire:click="deleteDocument({{ $document->id }})"
									flat
									color="red"
									icon="trash"
									sm />
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>

	<!-- Upload Document Modal -->
	<x-modal wire="showUploadModal">
		<x-card title="Upload Document">
			<div class="space-y-4">
				<div>
					<x-upload
							label="File"
							wire:model="file"
							id="file"
							class="mt-1 block w-full" />
				</div>

				<div>
					<x-input
							label="Name"
							wire:model="form.name"
							id="name"
							class="mt-1 block w-full" />
				</div>

				<div>
					<x-input
							label="Category"
							wire:model="form.category"
							id="category"
							class="mt-1 block w-full" />
				</div>

				<div>
					<x-textarea
							label="Description"
							wire:model="form.description"
							id="description"
							class="mt-1 block w-full" />
				</div>

				<div class="flex items-center">
					<x-checkbox
							label="Private"
							wire:model="form.is_private"
							id="is_private" />
				</div>
			</div>

			<x-slot:footer>
				<div class="flex justify-end gap-x-4">
					<x-button
							flat
							text="Cancel"
							wire:click="$toggle('showUploadModal')" />
					<x-button
							primary
							label="Upload"
							text="Upload"
							wire:click="uploadDocument"
							wire:loading.attr="disabled" />
				</div>
			</x-slot:footer>
		</x-card>
	</x-modal>

	<!-- View Document Modal -->
	<x-modal
			wire="showViewModal"
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