<?php

	use App\Livewire\Forms\CreateSubjectForm;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\Image\ImageService;
	use Illuminate\Routing\Redirector;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Illuminate\View\View;
	use Illuminate\Support\Facades\Auth;
	use Livewire\WithFileUploads;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Facades\Log;

	new #[Layout('layouts.negotiation')] class extends Component {
		use WithFileUploads;

		public Subject $subject;
		public ?Negotiation $negotiation = null;
		public CreateSubjectForm $createSubjectForm;
		public $currentStep = 1;
		public $totalSteps = 6;
		public $tempImages = [];

		// Contact information properties
		public $contactPhone = '';
		public $contactEmail = '';
		public $contactAddress = '';
		public $contactCity = '';
		public $contactState = '';
		public $contactZip = '';
		public $contactCountry = '';

		public function mount(Subject $subject, ?Negotiation $negotiation = null)
		{
			$this->subject = $subject;
			$this->negotiation = $negotiation;
			$this->createSubjectForm->fill($this->subject);

			// Load existing images from the polymorphic relationship
			$this->subject->load('images'); // Eager load images to avoid N+1 query issues
			$this->subject->images->each(function ($image) {
				// Use the url method to get the proper URL with error handling
				$this->tempImages[] = $image->url();
			});

			// Load contact information from the ContactPoint model and its related models
			$this->subject->load(['contactPoints.phone', 'contactPoints.email', 'contactPoints.address']);

			// Get the primary contact point if it exists
			$primaryContactPoint = $this->subject->contactPoints()->where('is_primary', true)->first();

			// If no primary contact point, get the first contact point
			if (!$primaryContactPoint && $this->subject->contactPoints->isNotEmpty()) {
				$primaryContactPoint = $this->subject->contactPoints->first();
			}

			// If a contact point exists, load its information
			if ($primaryContactPoint) {
				// Get phone number if it exists
				if ($primaryContactPoint->phone) {
					$this->contactPhone = $primaryContactPoint->phone->e164;
				}

				// Get email if it exists
				if ($primaryContactPoint->email) {
					$this->contactEmail = $primaryContactPoint->email->email;
				}

				// Get address if it exists
				if ($primaryContactPoint->address) {
					$this->contactAddress = $primaryContactPoint->address->address1;
					$this->contactCity = $primaryContactPoint->address->city;
					$this->contactState = $primaryContactPoint->address->region;
					$this->contactZip = $primaryContactPoint->address->postal_code;
					// Set country or default to US
					$this->contactCountry = $primaryContactPoint->address->address_country_iso ?? 'US';
				}
			} // If no contact exists but the subject has these fields (legacy data), use them
			else {
				$this->contactPhone = $this->subject->phone ?? '';
				$this->contactEmail = $this->subject->email ?? '';
				$this->contactAddress = $this->subject->address ?? '';
				$this->contactCity = $this->subject->city ?? '';
				$this->contactState = $this->subject->state ?? '';
				$this->contactZip = $this->subject->zip ?? '';
				$this->contactCountry = $this->subject->country ?? 'US';
			}
		}

		public function rendering(View $view):void
		{
			$view->layoutData(['negotiation' => $this->negotiation]);
		}

		public function nextStep()
		{
			if ($this->currentStep < $this->totalSteps) {
				$this->currentStep++;
				$this->dispatch('step-changed');
			}
		}

		public function previousStep()
		{
			if ($this->currentStep > 1) {
				$this->currentStep--;
				$this->dispatch('step-changed');
			}
		}

		public function goToStep($step):void
		{
			if ($step >= 1 && $step <= $this->totalSteps) {
				$this->currentStep = $step;
				$this->dispatch('step-changed');
			}
		}

		public function removeImage($index):void
		{
			if (isset($this->tempImages[$index])) {
				$image = $this->tempImages[$index];

				// If this is an existing image (string URL), delete it from the database and storage
				if (is_string($image)) {
					// Find the image in the database
					$existingImage = $this->subject->images->first(function ($img) use ($image) {
						return $img->url == $image || $img->url() == $image;
					});

					if ($existingImage) {
						// Use ImageService to delete the image
						$imageService = app(ImageService::class);
						$imageService->deleteImage($existingImage);
					}
				}

				// Remove from tempImages array (for both new uploads and existing images)
				unset($this->tempImages[$index]);
				$this->tempImages = array_values($this->tempImages);
			}
		}

		public function setAsPrimaryImage($index):void
		{
			if (isset($this->tempImages[$index])) {
				$image = $this->tempImages[$index];

				// If this is an existing image (string URL), find the image and set it as primary
				if (is_string($image)) {
					$existingImage = $this->subject->images->first(function ($img) use ($image) {
						return $img->url == $image || $img->url() == $image;
					});

					if ($existingImage) {
						// Use ImageService to set the image as primary
						$imageService = app(ImageService::class);
						$imageService->setPrimaryImage($existingImage);

						// Show a notification
						session()->flash('message', 'Primary image updated successfully.');
					}
				}
			}
		}

		public function updateSubject():Redirector
		{
			// Validate the form
			$validated = $this->createSubjectForm->validate();

			// Update the subject
			$this->subject->update($validated);

			// Handle contact information
			$this->updateContactInformation();

			// Handle new image uploads using ImageService
			$newImages = array_filter($this->tempImages, function ($image) {
				return !is_string($image);
			});

			if (!empty($newImages)) {
				$imageService = app(ImageService::class);
				$imageService->uploadImagesForModel(
					$newImages,
					$this->subject,
					'subjects',
					's3_public'
				);
			}

			// Redirect back to the subject page or negotiation page
			if ($this->negotiation) {
				return redirect()->route('negotiation-noc', [
					'negotiation' => $this->negotiation->title,
					'tenantSubdomain' => tenant()->subdomain
				]);
			}

			return redirect()->route('dashboard.negotiations', tenant()->subdomain);
		}

		/**
		 * Update or create contact information for the subject
		 */
		protected function updateContactInformation():void
		{
			// Handle phone number
			if (!empty($this->contactPhone)) {
				// Get or create a phone contact point
				$phoneContactPoint = $this->subject->contactPoints()
					->where('kind', 'phone')
					->first();

				if (!$phoneContactPoint) {
					// Create a new phone contact point
					$phoneContactPoint = $this->subject->contactPoints()->create([
						'tenant_id' => tenant()->id,
						'kind' => 'phone',
						'is_primary' => !$this->subject->contactPoints()->where('is_primary', true)->exists(),
						'is_verified' => false,
						'priority' => 0,
					]);
				}

				// Update or create phone number
				$phone = $phoneContactPoint->phone()->first();

				if ($phone) {
					$phone->update(['e164' => $this->contactPhone]);
				} else {
					$phoneContactPoint->phone()->create([
						'e164' => $this->contactPhone,
						'country_iso' => 'US',
					]);
				}
			}

			// Handle email
			if (!empty($this->contactEmail)) {
				// Get or create an email contact point
				$emailContactPoint = $this->subject->contactPoints()
					->where('kind', 'email')
					->first();

				if (!$emailContactPoint) {
					// Create a new email contact point
					$emailContactPoint = $this->subject->contactPoints()->create([
						'tenant_id' => tenant()->id,
						'kind' => 'email',
						'is_primary' => !$this->subject->contactPoints()->where('is_primary', true)->exists(),
						'is_verified' => false,
						'priority' => 0,
					]);
				}

				// Update or create email
				$email = $emailContactPoint->email()->first();

				if ($email) {
					$email->update(['email' => $this->contactEmail]);
				} else {
					$emailContactPoint->email()->create([
						'email' => $this->contactEmail,
					]);
				}
			}

			// Handle address
			if (!empty($this->contactAddress) || !empty($this->contactCity) || !empty($this->contactState)) {
				// Get or create an address contact point
				$addressContactPoint = $this->subject->contactPoints()
					->where('kind', 'address')
					->first();

				if (!$addressContactPoint) {
					// Create a new address contact point
					$addressContactPoint = $this->subject->contactPoints()->create([
						'tenant_id' => tenant()->id,
						'kind' => 'address',
						'is_primary' => !$this->subject->contactPoints()->where('is_primary', true)->exists(),
						'is_verified' => false,
						'priority' => 0,
					]);
				}

				// Update or create address
				$address = $addressContactPoint->address()->first();

				$addressData = [
					'address1' => $this->contactAddress,
					'city' => $this->contactCity,
					'region' => $this->contactState,
					'postal_code' => $this->contactZip,
					'country_iso' => $this->contactCountry?: 'US',
				];

				if ($address) {
					$address->update($addressData);
				} else {
					$addressContactPoint->address()->create($addressData);
				}
			}

			// For backward compatibility, ensure there's at least one primary contact point
			if (!$this->subject->contactPoints()->where('is_primary',
					true)->exists() && $this->subject->contactPoints()->exists()) {
				$this->subject->contactPoints()->first()->update(['is_primary' => true]);
			}
		}

		public function cancel()
		{
			// Redirect back to the subject page or negotiation page
			if ($this->negotiation) {
				return redirect()->route('negotiation-noc', [
					'negotiation' => $this->negotiation->title,
					'tenantSubdomain' => tenant()->subdomain
				]);
			}

			return redirect()->route('dashboard.negotiations', tenant()->subdomain);
		}
	}

?>

<div class="max-w-7xl mx-auto bg-dark-100 dark:bg-dark-800 p-8 mt-4 rounded-lg">
	<div class="px-4 sm:px-8 text-center space-y-3">
		<h1 class="text-2xl text-gray-400 font-semibold uppercase">Update Subject</h1>
		<p class="text-xs">Updating subject: <span class="text-primary-400">{{ $subject->name }}</span></p>
	</div>

	<!-- Progress Bar -->
	<div class="mb-8 mt-4">
		<div class="flex justify-between mb-2">
			@for ($i = 1; $i <= $totalSteps; $i++)
				<button
						wire:click="goToStep({{ $i }})"
						class="flex flex-col items-center group">
					<div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= $i ? 'bg-primary-500' : 'bg-dark-400 dark:bg-dark-800' }} transition-colors duration-300">
						<span class="text-white">{{ $i }}</span>
					</div>
					<span class="text-xs mt-1 {{ $currentStep >= $i ? 'text-primary-400' : 'text-dark-500' }} group-hover:text-primary-300 transition-colors duration-300">
						@switch($i)
							@case(1)
								Basic Info
								@break
							@case(2)
								Status
									@break
							@case(3)
								Contact
									@break
							@case(4)
								Employment
									@break
							@case(5)
								History
									@break
							@case(6)
								Images
									@break
						@endswitch
					</span>
				</button>

				@if ($i < $totalSteps)
					<div class="flex-1 h-0.5 self-center {{ $currentStep > $i ? 'bg-primary-500' : 'bg-gray-700' }} transition-colors duration-300"></div>
				@endif
			@endfor
		</div>
	</div>

	<form
			id="editSubjectForm"
			wire:submit.prevent="updateSubject"
			class="space-y-6">

		<!-- Step 1: Basic Information -->
		<div
				class="mb-6"
				x-data="{}"
				x-show="$wire.currentStep == 1">
			<h2 class="text-lg font-semibold dark:text-white text-dark-800">Basic Information</h2>
			<p class="mb-4 text-sm text-dark-500">Enter the basic details about this subject</p>

			<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
				<x-input
						icon="user"
						label="Name *"
						wire:model="createSubjectForm.name"
						placeholder="Enter subject name" />


				<x-date
						label="Date of Birth"
						wire:model="createSubjectForm.date_of_birth"
						placeholder="Enter date of birth" />

				<x-input
						icon="user"
						label="Gender"
						wire:model="createSubjectForm.gender"
						placeholder="Enter gender" />

				<x-input
						icon="arrow-trending-up"
						label="Height"
						wire:model="createSubjectForm.height"
						placeholder="Enter height" />

				<x-input
						icon="scale"
						label="Weight"
						wire:model="createSubjectForm.weight"
						placeholder="Enter weight" />

				<x-input
						icon="swatch"
						label="Hair Color"
						wire:model="createSubjectForm.hair_color"
						placeholder="Enter hair color" />

				<x-input
						icon="eye"
						label="Eye Color"
						wire:model="createSubjectForm.eye_color"
						placeholder="Enter eye color" />
				<div class="col-span-2">
					<x-tag
							icon="identification"
							label="Alias"
							wire:model="createSubjectForm.alias"
							placeholder="Enter alias"
							hint="Enter any other names this subject may be known by followed by enter."
					/>
				</div>
				<div class="col-span-2">
					<x-tag
							placeholder="Add risk factors"
							icon="exclamation-triangle"
							label="Risk Factors"
							wire:model="createSubjectForm.risk_factors"
							hint="Enter known risk factors followed by enter."
					/>
				</div>
			</div>
		</div>

		<!-- Step 2: Status Information -->
		<div
				class="mb-6"
				x-data="{}"
				x-show="$wire.currentStep == 2">
			<h2 class="text-lg font-semibold dark:text-white text-dark-800">Status Information</h2>
			<p class="mb-4 text-sm text-dark-500">Enter status information for this subject</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-select.styled
						searchable
						class="w-full"
						icon="face-smile"
						label="Current Mood *"
						wire:model="createSubjectForm.current_mood"
						:options="collect(App\Enums\Subject\MoodLevels::cases())->map(fn($mood) => [
						'label' => $mood->label(),
						'value' => $mood->value,
					])->toArray()" />

				<x-select.styled
						searchable
						class="w-full"
						icon="flag"
						label="Status *"
						wire:model="createSubjectForm.status"
						:options="collect(App\Enums\Subject\SubjectNegotiationStatuses::cases())->map(fn($status) => [
						'label' => $status->label(),
						'value' => $status->value,
					])->toArray()" />
			</div>
		</div>

		<!-- Step 3: Contact Information -->
		<div
				class="mb-6"
				x-data="{}"
				x-show="$wire.currentStep == 3">
			<h2 class="text-lg font-semibold dark:text-white text-dark-800">Contact Information</h2>
			<p class="mb-4 text-sm text-dark-500">Enter contact details for this subject</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-input
						icon="phone"
						label="Phone"
						wire:model="contactPhone"
						placeholder="Enter phone number" />

				<x-input
						icon="envelope"
						label="Email"
						wire:model="contactEmail"
						placeholder="Enter email address" />

				<x-input
						icon="home"
						label="Address"
						wire:model="contactAddress"
						placeholder="Enter street address" />

				<x-input
						icon="building-office"
						label="City"
						wire:model="contactCity"
						placeholder="Enter city" />

				<x-input
						icon="map"
						label="State"
						wire:model="contactState"
						placeholder="Enter state" />

				<x-input
						icon="hashtag"
						label="ZIP Code"
						wire:model="contactZip"
						placeholder="Enter ZIP code" />

				<x-input
						icon="globe-alt"
						label="Country"
						wire:model="contactCountry"
						placeholder="Enter country" />
			</div>
		</div>

		<!-- Step 4: Employment Information -->
		<div
				class="mb-6"
				x-data="{}"
				x-show="$wire.currentStep == 4">
			<h2 class="text-lg font-semibold dark:text-white text-dark-800">Employment Information</h2>
			<p class="mb-4 text-sm text-dark-500">Enter employment details for this subject</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-input
						icon="briefcase"
						label="Occupation"
						wire:model="createSubjectForm.occupation"
						placeholder="Enter occupation" />

				<x-input
						icon="building-office-2"
						label="Employer"
						wire:model="createSubjectForm.employer"
						placeholder="Enter employer" />
			</div>
		</div>

		<!-- Step 5: History and Risk Factors -->
		<div
				class="mb-6"
				x-data="{}"
				x-show="$wire.currentStep == 5">
			<h2 class="text-lg font-semibold dark:text-white text-dark-800">History and Risk Factors</h2>
			<p class="mb-4 text-sm text-dark-500">Enter history and risk factors for this subject</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-textarea
						label="Identifying Features"
						wire:model="createSubjectForm.identifying_features"
						placeholder="Enter identifying features"
						rows="3" />
				<x-textarea
						label="Mental Health History"
						wire:model="createSubjectForm.mental_health_history"
						placeholder="Enter mental health history"
						rows="3" />

				<x-textarea
						label="Criminal History"
						wire:model="createSubjectForm.criminal_history"
						placeholder="Enter criminal history"
						rows="3" />

				<x-textarea
						label="Substance Abuse History"
						wire:model="createSubjectForm.substance_abuse_history"
						placeholder="Enter substance abuse history"
						rows="3" />

				<x-textarea
						label="Known Weapons"
						wire:model="createSubjectForm.known_weapons"
						placeholder="Enter known weapons"
						rows="3" />
				<x-textarea
						label="Notes"
						wire:model="createSubjectForm.notes"
						placeholder="Enter additional notes"
						rows="3" />
			</div>
		</div>

		<!-- Step 6: Images -->
		<div
				class="mb-6"
				x-data="{}"
				x-show="$wire.currentStep == 6">
			<h2 class="text-lg font-semibold dark:text-white text-dark-800">Subject Images</h2>
			<p class="mb-4 text-sm text-dark-500">Upload images of the subject</p>

			<div class="mb-6">
				<label class="block text-sm font-medium text-gray-300 mb-2">Upload Images</label>
				<div class="flex items-center justify-center w-full">
					<label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-600 rounded-lg cursor-pointer hover:bg-dark-600 transition-colors duration-300">
						<div class="flex flex-col items-center justify-center pt-7">
							<svg
									class="w-8 h-8 text-gray-400"
									fill="none"
									stroke="currentColor"
									viewBox="0 0 24 24"
									xmlns="http://www.w3.org/2000/svg">
								<path
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
							</svg>
							<p class="pt-1 text-sm text-gray-400">Drag and drop images or click to browse</p>
						</div>
						<input
								type="file"
								class="hidden"
								wire:model="tempImages"
								multiple
								accept="image/*">
					</label>
				</div>
				<div
						wire:loading
						wire:target="tempImages"
						class="mt-2 text-sm text-primary-400">
					Uploading...
				</div>
			</div>

			<!-- Image Preview -->
			@if(count($tempImages) > 0)
				<div class="mt-4">
					<h3 class="text-md font-medium text-gray-300 mb-2">Subject Images</h3>

					<!-- Display existing images first -->
					@if(count(array_filter($tempImages, 'is_string')) > 0)
						<h4 class="text-sm font-medium text-primary-400 mb-2">Existing Images</h4>
						<div class="grid grid-cols-6 md:grid-cols-8 gap-4 mb-6">
							@foreach($tempImages as $index => $image)
								@if(is_string($image))
									<div class="relative group">
										<div class="aspect-w-1 aspect-h-1 rounded-lg overflow-hidden bg-dark-600">
											<img
													src="{{ $image }}"
													alt="Subject image"
													class="w-full h-full object-cover">

											<!-- Primary badge -->
											@php
												$isPrimary = false;
												$existingImage = $subject->images->first(function ($img) use ($image) {
													return $img->url == $image || $img->url() == $image;
												});
												if ($existingImage && $existingImage->is_primary) {
													$isPrimary = true;
												}
											@endphp

											@if($isPrimary)
												<div class="absolute top-2 left-2 bg-primary-500 text-white text-xs px-2 py-1 rounded-full">
													Primary
												</div>
											@endif
										</div>

										<!-- Image info -->
										<div class="mt-1 text-xs text-gray-400">
											@if($existingImage)
												<p class="truncate">{{ $existingImage->original_filename ?? 'Image' }}</p>
												@if($existingImage->size)
													<p>{{ round($existingImage->size / 1024) }} KB</p>
												@endif
											@endif
										</div>

										<!-- Action buttons -->
										<div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
											<!-- Set as primary button -->
											@if(!$isPrimary)
												<button
														type="button"
														wire:click="setAsPrimaryImage({{ $index }})"
														class="bg-primary-500 text-white rounded-full p-1"
														title="Set as primary image">
													<svg
															class="w-4 h-4"
															fill="none"
															stroke="currentColor"
															viewBox="0 0 24 24"
															xmlns="http://www.w3.org/2000/svg">
														<path
																stroke-linecap="round"
																stroke-linejoin="round"
																stroke-width="2"
																d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
													</svg>
												</button>
											@endif

											<!-- Delete button -->
											<button
													type="button"
													x-data="{}"
													@click="if (confirm('Are you sure you want to delete this image?')) { $wire.removeImage({{ $index }}) }"
													class="bg-red-500 text-white rounded-full p-1"
													title="Delete image">
												<svg
														class="w-4 h-4"
														fill="none"
														stroke="currentColor"
														viewBox="0 0 24 24"
														xmlns="http://www.w3.org/2000/svg">
													<path
															stroke-linecap="round"
															stroke-linejoin="round"
															stroke-width="2"
															d="M6 18L18 6M6 6l12 12"></path>
												</svg>
											</button>
										</div>
									</div>
								@endif
							@endforeach
						</div>
					@endif

					<!-- Display new uploads -->
					@if(count(array_filter($tempImages, function($img) { return !is_string($img); })) > 0)
						<h4 class="text-sm font-medium text-green-400 mb-2">New Uploads</h4>
						<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
							@foreach($tempImages as $index => $image)
								@if(!is_string($image))
									<div class="relative group">
										<div class="aspect-w-1 aspect-h-1 rounded-lg overflow-hidden bg-dark-600">
											<img
													src="{{ $image->temporaryUrl() }}"
													alt="Subject image"
													class="w-full h-full object-cover">
										</div>

										<!-- Image info -->
										<div class="mt-1 text-xs text-gray-400">
											<p class="truncate">{{ $image->getClientOriginalName() }}</p>
											<p>{{ round($image->getSize() / 1024) }} KB</p>
										</div>

										<!-- Delete button -->
										<button
												type="button"
												x-data="{}"
												@click="if (confirm('Are you sure you want to remove this upload?')) { $wire.removeImage({{ $index }}) }"
												class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
												title="Remove upload">
											<svg
													class="w-4 h-4"
													fill="none"
													stroke="currentColor"
													viewBox="0 0 24 24"
													xmlns="http://www.w3.org/2000/svg">
												<path
														stroke-linecap="round"
														stroke-linejoin="round"
														stroke-width="2"
														d="M6 18L18 6M6 6l12 12"></path>
											</svg>
										</button>
									</div>
								@endif
							@endforeach
						</div>
					@endif
				</div>
			@else
				<div class="mt-4 text-center p-6 bg-dark-600 rounded-lg">
					<p class="text-gray-400">No images uploaded yet. Use the upload area above to add images.</p>
				</div>
			@endif

			<!-- Flash message for primary image update -->
			@if(session()->has('message'))
				<div class="mt-4 p-3 bg-primary-500 bg-opacity-20 text-primary-300 rounded-lg">
					{{ session('message') }}
				</div>
			@endif
		</div>

		<!-- Navigation Buttons -->
		<div class="flex items-center justify-between gap-4 mt-8">
			<div>
				@if($currentStep > 1)
					<x-button
							sm
							wire:click="previousStep"
							color="secondary">
						Previous
					</x-button>
				@endif
			</div>

			<div class="flex items-center gap-4">
				<x-button
						sm
						wire:click="cancel"
						color="secondary">
					Cancel
				</x-button>

				@if($currentStep < $totalSteps)
					<x-button
							sm
							wire:click="nextStep"
							primary>
						Next
					</x-button>
				@else
					<x-button
							sm
							type="submit"
							primary>
						Update Subject
					</x-button>
				@endif
			</div>
		</div>
	</form>
</div>

@push('scripts')
	<script>
		// Prevent form submission on Enter key press
		document.getElementById('editSubjectForm').addEventListener('keydown', function (event) {
			if (event.key === 'Enter') {
				event.preventDefault() // Stop the form from submitting
			}
		})

		// Initialize Alpine.js components for step visibility
		document.addEventListener('alpine:init', () => {
			// Make sure all steps are properly shown/hidden based on current step
			window.addEventListener('livewire:initialized', () => {
				// Force Alpine to evaluate x-show directives
				document.querySelectorAll('[x-data]').forEach(el => {
					Alpine.initTree(el)
				})
			})

			// Re-evaluate x-show directives when step changes
			window.addEventListener('step-changed', () => {
				document.querySelectorAll('[x-data]').forEach(el => {
					Alpine.initTree(el)
				})
			})
		})
	</script>
@endpush
