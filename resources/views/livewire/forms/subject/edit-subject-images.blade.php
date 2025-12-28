<?php

	use App\Models\Image;
	use App\Models\Subject;
	use App\Services\Image\ImageService;
	use App\Services\Subject\SubjectFetchingService;
	use Illuminate\Http\UploadedFile;
	use Illuminate\Support\Arr;
	use Illuminate\Validation\ValidationException;
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

		public function deleteUpload(array $content):void
		{
			if (!$this->newImages) {
				return;
			}

			$files = Arr::wrap($this->newImages);

			$file = collect($files)->filter(fn(UploadedFile $item
			) => $item->getFilename() === $content['temporary_name'])->first();

			rescue(fn() => $file->delete(), report: false);
			$collect = collect($files)->filter(fn(UploadedFile $item
			) => $item->getFilename() !== $content['temporary_name']);

			$this->newImages = is_array($this->newImages)? $collect->toArray() : $collect->first();

		}

		public function saveNewImages():void
		{
			if (empty($this->newImages)) {
				return;
			}

			$this->validate([
				'newImages' => ['array', 'max:20'],
				'newImages.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
			]);

			$service = app(ImageService::class);
			$service->uploadImagesForModel(
				$this->newImages,
				$this->subject,
				'subjects',
				's3_public'
			);

			$this->subject->load('images');
			$this->reset(['newImages', 'backup']);
		}

		public function deleteExistingImage(int $imageId):void
		{
			/** @var Image|null $image */
			$image = $this->subject->images->firstWhere('id', $imageId);
			if (!$image) {
				return;
			}

			app(ImageService::class)->deleteImage($image);
			$this->subject->load('images');
		}

		public function setPrimaryImage(int $imageId):void
		{
			/** @var Image|null $image */
			$image = $this->subject->images->firstWhere('id', $imageId);
			if (!$image) {
				return;
			}

			app(ImageService::class)->setPrimaryImage($image);
			$this->subject->load('images');
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
							class="w-full h-auto object-cover rounded-lg"
							src="{{ $image->url }}"
							alt="{{ $image->alt_text ?? 'Subject Image' }}">
					<div class="absolute top-1 left-1">
						@if($image->is_primary)
							<span class="px-2 py-0.5 text-xs rounded bg-green-600/80 text-white">Primary</span>
						@endif
					</div>
					<div class="absolute top-1 right-1 flex gap-1">
						@if(!$image->is_primary)
							<x-button.circle
									type="button"
									icon="sparkles"
									sm
									wire:click="setPrimaryImage({{ $image->id }})" />
						@endif
						<x-button.circle
								type="button"
								color="red"
								icon="x-mark"
								sm
								wire:click="deleteExistingImage({{ $image->id }})" />
					</div>
				</div>
			@endforeach
		</div>
	@endif
	
	<div>
		<x-upload
				delete
				multiple
				label="Upload Images"
				wire:model="newImages" />

		@if($newImages)
			<div class="mt-4 flex justify-end">
				<x-button wire:click="saveNewImages()">Save</x-button>
			</div>
		@endif

	</div>
</div>
