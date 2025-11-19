<?php

	use App\Models\Subject;
	use App\Services\Subject\SubjectFetchingService;
	use Illuminate\Http\UploadedFile;
	use Illuminate\Support\Arr;
	use Livewire\Volt\Component;
	use Livewire\WithFileUploads;

	new class extends Component {
		use WithFileUploads;

		public Subject $subject;
		public array $newImages = [];
		public array $backup = [];

		public function mount(int $subjectId):void
		{
			$this->subject = app(SubjectFetchingService::class)
				->fetchSubjectById($subjectId)->load('images');
		}

		public function updatingNewImages():void
		{
			// Store the already selected files temporarily before a new selection
			$this->backup = $this->newImages;
		}

		public function updatedNewImages():void
		{
			if (!$this->newImages) {
				return;
			}

			// Merge the newly selected files with the existing ones
			$files = Arr::flatten(array_merge($this->backup, [$this->newImages]));

			// Remove duplicates by original client filename and reindex
			$this->newImages = array_values(
				collect($files)
					->unique(fn(UploadedFile $item) => $item->getClientOriginalName())
					->toArray()
			);
		}

		public function deleteUpload(int $index):void
		{
			if (!array_key_exists($index, $this->newImages)) {
				return;
			}

			unset($this->newImages[$index]);
			$this->newImages = array_values($this->newImages);
		}
	}

?>

<div>
	@if($subject->images->count())
		<h3>Current Images</h3>
		<div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-2">
			@foreach ($subject->images as $image)
				<div
						class="relative"
						wire:key="existing-image-{{ $image->id }}">
					<img
							class="w-full h-full object-cover rounded-lg"
							src="{{ $image->url }}"
							alt="{{ $image->alt_text ?? 'Subject Image' }}">
					<div class="absolute top-1 right-1 flex gap-1">
						@if(!$image->is_primary)
							<x-button.circle
									icon="sparkles"
									sm />
						@endif
						<x-button.circle
								color="red"
								icon="x-mark"
								sm />
					</div>
				</div>
			@endforeach
		</div>
	@endif

	@if($newImages)
		<h3 class="mt-4">New Images</h3>
		<div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-2">
			@foreach ($newImages as $image)
				<div
						class="relative"
						wire:key="new-image-{{ $loop->index }}">
					<img
							class="w-full h-full object-cover rounded-lg"
							src="{{ $image->temporaryUrl() }}"
							alt="New Subject Image">
					<div class="absolute top-1 right-1">
						<x-button.circle
								type="button"
								color="red"
								icon="x-mark"
								sm
								wire:click="deleteUpload({{ $loop->index }})"
						/>
					</div>
				</div>
			@endforeach
		</div>
	@endif
	<div>
		<x-upload
				multiple
				label="Upload Images"
				wire:model="newImages" />
	</div>
</div>
