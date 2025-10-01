<?php

	use App\Events\Subject\ContactUpdatedEvent;
	use App\Livewire\Forms\CreateContactPointForm;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Models\ContactPoint;
	use App\Services\Negotiation\NegotiationFetchingService;
	use App\Services\Subject\SubjectFetchingService;
	use App\Services\ContactPoint\ContactPointFetchingService;
	use App\Services\ContactPoint\ContactPointUpdateService;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\On;
	use Livewire\Volt\Component;

	new #[Layout('layouts.negotiation')] class extends Component {
		public Negotiation $negotiation;
		public Subject $subject;
		public ContactPoint $contactPoint;
		public CreateContactPointForm $form;

		public function mount($negotiationId, $subjectId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)
				->getNegotiationById($negotiationId);

			$this->subject = app(SubjectFetchingService::class)
				->fetchSubjectById($subjectId);

		}

		#[On('load-contact')]
		public function loadForm(int $contactPointId)
		{

//			dd($contactPointId);
			$this->contactPoint = app(ContactPointFetchingService::class)
				->getContactPointById($contactPointId);

			// Fill the form with the contact point data
			$this->form->fill([
				'tenant_id' => $this->contactPoint->tenant_id,
				'subject_id' => $this->contactPoint->subject_id,
				'kind' => $this->contactPoint->kind,
				'label' => $this->contactPoint->label,
				'is_primary' => $this->contactPoint->is_primary,
				'is_verified' => $this->contactPoint->is_verified,
				'priority' => $this->contactPoint->priority,
			]);

			// Fill the specific contact point type data
			if ($this->contactPoint->kind === 'email' && $this->contactPoint->email) {
				$this->form->email = $this->contactPoint->email->email;
			} elseif ($this->contactPoint->kind === 'phone' && $this->contactPoint->phone) {
				$this->form->e164 = $this->contactPoint->phone->e164;
				$this->form->ext = $this->contactPoint->phone->ext;
				$this->form->phone_country_iso = $this->contactPoint->phone->country_iso;
			} elseif ($this->contactPoint->kind === 'address' && $this->contactPoint->address) {
				$this->form->address1 = $this->contactPoint->address->address1;
				$this->form->address2 = $this->contactPoint->address->address2;
				$this->form->city = $this->contactPoint->address->city;
				$this->form->region = $this->contactPoint->address->region;
				$this->form->postal_code = $this->contactPoint->address->postal_code;
				$this->form->address_country_iso = $this->contactPoint->address->country_iso;
				$this->form->latitude = $this->contactPoint->address->latitude;
				$this->form->longitude = $this->contactPoint->address->longitude;
			}
		}

		public function cancel()
		{
			$this->dispatch('closeModal');
		}

		public function updateContactPoint()
		{
			try {
				// CRITICAL: Explicitly set subject_id before validation
				// This ensures it's set even if the wire:model binding fails
				if ($this->subject && $this->subject->id) {
					$this->form->subject_id = $this->subject->id;
					\Illuminate\Support\Facades\Log::info('Pre-validation: Setting form.subject_id from $this->subject: '.$this->subject->id);
				}

				// Validate the form
				$validated = $this->form->validate();

				// Log the validated data for debugging
				\Illuminate\Support\Facades\Log::debug('Validated data: '.json_encode($validated));

				// Ensure all required fields are present based on contact point type
				$kind = $validated['kind'] ?? 'email';

				// Make sure common fields are set with defaults if missing
				if (!isset($validated['tenant_id'])) {
					$validated['tenant_id'] = $this->contactPoint->tenant_id;
				}

				// Ensure subject_id is always set - this is critical
				if (!isset($validated['subject_id']) || empty($validated['subject_id'])) {
					// Try multiple fallbacks to ensure we have a valid subject_id
					if ($this->subject && $this->subject->id) {
						$validated['subject_id'] = $this->subject->id;
						\Illuminate\Support\Facades\Log::info('Setting subject_id from $this->subject: '.$this->subject->id);
					} elseif ($this->contactPoint && $this->contactPoint->subject_id) {
						$validated['subject_id'] = $this->contactPoint->subject_id;
						\Illuminate\Support\Facades\Log::info('Setting subject_id from $this->contactPoint: '.$this->contactPoint->subject_id);
					} elseif (request()->has('subject_id_backup')) {
						// Use the backup field if available
						$validated['subject_id'] = request()->input('subject_id_backup');
						\Illuminate\Support\Facades\Log::info('Setting subject_id from backup field: '.$validated['subject_id']);
					} else {
						\Illuminate\Support\Facades\Log::error('Failed to set subject_id - no valid source found');
						throw new \Exception('The subject id field is required and could not be determined automatically.');
					}
				} else {
					\Illuminate\Support\Facades\Log::info('Using subject_id from validated data: '.$validated['subject_id']);
				}

				if (!isset($validated['label'])) {
					$validated['label'] = $this->contactPoint->label ?? '';
				}

				if (!isset($validated['is_primary'])) {
					$validated['is_primary'] = $this->contactPoint->is_primary ?? false;
				}

				if (!isset($validated['is_verified'])) {
					$validated['is_verified'] = $this->contactPoint->is_verified ?? false;
				}

				if (!isset($validated['priority'])) {
					$validated['priority'] = $this->contactPoint->priority ?? 0;
				}

				// Set type-specific defaults based on the selected kind
				if ($kind === 'email') {
					if (!isset($validated['email']) || empty($validated['email'])) {
						throw new \Exception('Email address is required for email contact points.');
					}
				} elseif ($kind === 'phone') {
					if (!isset($validated['e164']) || empty($validated['e164'])) {
						throw new \Exception('Phone number is required for phone contact points.');
					}

					// Set defaults for optional phone fields
					if (!isset($validated['ext'])) {
						$validated['ext'] = '';
					}

					if (!isset($validated['phone_country_iso'])) {
						$validated['phone_country_iso'] = 'US';
					}
				} elseif ($kind === 'address') {
					if (!isset($validated['address1']) || empty($validated['address1'])) {
						throw new \Exception('Address line 1 is required for address contact points.');
					}

					// Set defaults for optional address fields
					if (!isset($validated['address2'])) {
						$validated['address2'] = '';
					}

					if (!isset($validated['city'])) {
						$validated['city'] = '';
					}

					if (!isset($validated['region'])) {
						$validated['region'] = '';
					}

					if (!isset($validated['postal_code'])) {
						$validated['postal_code'] = '';
					}

					if (!isset($validated['address_country_iso'])) {
						$validated['address_country_iso'] = 'US';
					}

					if (!isset($validated['latitude'])) {
						$validated['latitude'] = null;
					}

					if (!isset($validated['longitude'])) {
						$validated['longitude'] = null;
					}
				}

				// Final check to ensure subject_id is set before updating
				if (!isset($validated['subject_id']) || empty($validated['subject_id'])) {
					// One last attempt - force set the subject_id directly
					if ($this->subject && $this->subject->id) {
						$validated['subject_id'] = $this->subject->id;
						\Illuminate\Support\Facades\Log::warning('LAST RESORT: Forcing subject_id from $this->subject: '.$this->subject->id);
					} else {
						// Log all available data to help diagnose the issue
						\Illuminate\Support\Facades\Log::error('CRITICAL ERROR: subject_id is still missing before update call');
						\Illuminate\Support\Facades\Log::error('Form data: '.json_encode($this->form));
						\Illuminate\Support\Facades\Log::error('Subject data: '.($this->subject? json_encode($this->subject) : 'null'));
						\Illuminate\Support\Facades\Log::error('ContactPoint data: '.json_encode($this->contactPoint));
						\Illuminate\Support\Facades\Log::error('Request data: '.json_encode(request()->all()));

						throw new \Exception('The subject id field is required but is still missing before update. Please contact support with reference ID: '.now()->timestamp);
					}
				}

				// Log the final validated data before update
				\Illuminate\Support\Facades\Log::info('Final validated data before update: '.json_encode($validated));

				// Double-check subject_id one more time
				if (!isset($validated['subject_id']) || empty($validated['subject_id'])) {
					throw new \Exception('CRITICAL: Subject ID is still missing after all attempts to set it.');
				}

				// Update the contact point
				try {
					$result = app(ContactPointUpdateService::class)->updateContactPoint($this->contactPoint->id,
						$validated);
					\Illuminate\Support\Facades\Log::info('Contact point updated successfully: '.$this->contactPoint->id);

					// Flash success message
					session()->flash('message', 'Contact point updated successfully.');

				} catch (\Exception $updateException) {
					\Illuminate\Support\Facades\Log::error('Error in ContactPointUpdateService: '.$updateException->getMessage());
					\Illuminate\Support\Facades\Log::error('With data: '.json_encode($validated));
					throw $updateException; // Re-throw to be caught by the outer try-catch
				}
			} catch (\Exception $e) {
				// Log the detailed error
				\Illuminate\Support\Facades\Log::error('Error updating contact point: '.$e->getMessage());
				\Illuminate\Support\Facades\Log::error('Error trace: '.$e->getTraceAsString());

				// Flash error message with more details in development
				if (config('app.env') === 'local' || config('app.debug')) {
					session()->flash('error', 'Failed to update contact point: '.$e->getMessage());
				} else {
					session()->flash('error', 'Failed to update contact point. Please try again.');
				}

				// Return without redirecting to show the error
				return null;
			}
			event(new ContactUpdatedEvent($this->subject->id, $this->contactPoint->id));

			$this->dispatch('closeModal');
		}
	}

?>
<div class="max-w-7xl mx-auto bg-white dark:bg-dark-700 p-8 mt-4 rounded-lg shadow-sm">
	<form
			wire:submit.prevent="updateContactPoint"
			class="space-y-6 mt-6"
			id="contactPointForm">
		<!-- Hidden input for subject_id - using wire:model.defer to ensure it's set -->
		<input
				type="hidden"
				wire:model.defer="form.subject_id"
				id="subject_id_field"
				value="{{ $subject->id }}">

		<!-- Backup hidden input for subject_id without wire:model to ensure it's submitted -->
		<input
				type="hidden"
				name="subject_id_backup"
				id="subject_id_backup"
				value="{{ $subject->id }}">

		<h2 class="text-lg font-semibold text-dark-500 dark:text-dark-100 mb-4">Contact Information</h2>
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
			<!-- Contact Type -->
			<div>
				<x-select.styled
						label="Contact Type"
						wire:model="form.kind"
						:options="[
                            ['value' => 'email', 'label' => 'Email'],
                            ['value' => 'phone', 'label' => 'Phone'],
                            ['value' => 'address', 'label' => 'Address'],
                        ]"
						class="w-full">
				</x-select.styled>
			</div>

			<!-- Label -->
			<div>
				<x-select.styled
						label="Label"
						wire:model="form.label"
						:options="[
                            ['value' => 'home', 'label' => 'Home'],
                            ['value' => 'work', 'label' => 'Work'],
                            ['value' => 'billing', 'label' => 'Billing'],
                            ['value' => 'other', 'label' => 'Other'],
                        ]"
						class="w-full">
				</x-select.styled>
			</div>

			<!-- Is Primary -->
			<div>
				<x-checkbox
						label="Primary Contact"
						wire:model="form.is_primary"
						class="" />
			</div>

			<!-- Is Verified -->
			<div>
				<x-checkbox
						label="Verified"
						wire:model="form.is_verified"
						class="" />
			</div>

			<!-- Email fields (shown only when kind is email) -->
			<div
					class="col-span-2"
					x-show="$wire.form.kind === 'email'">
				<x-input
						label="Email Address"
						placeholder="example@example.com"
						wire:model="form.email"
						class="w-full" />
			</div>

			<!-- Phone fields (shown only when kind is phone) -->
			<div
					class="col-span-2 space-y-4"
					x-show="$wire.form.kind === 'phone'">
				<x-input
						label="Phone Number (E.164 format)"
						placeholder="+14155550123"
						wire:model="form.e164"
						class="w-full" />

				<div class="grid grid-cols-2 gap-4">
					<x-input
							label="Extension"
							placeholder="123"
							wire:model="form.ext"
							class="w-full" />

					<x-input
							label="Country ISO"
							placeholder="US"
							wire:model="form.phone_country_iso"
							class="w-full" />
				</div>
			</div>

			<!-- Address fields (shown only when kind is address) -->
			<div
					class="col-span-2 space-y-4"
					x-show="$wire.form.kind === 'address'">
				<x-input
						label="Address Line 1"
						placeholder="123 Main St"
						wire:model="form.address1"
						class="w-full" />

				<x-input
						label="Address Line 2"
						placeholder="Apt 4B"
						wire:model="form.address2"
						class="w-full" />

				<div class="grid grid-cols-2 gap-4">
					<x-input
							label="City"
							placeholder="San Francisco"
							wire:model="form.city"
							class="w-full" />

					<x-input
							label="Region/State"
							placeholder="CA"
							wire:model="form.region"
							class="w-full" />
				</div>

				<div class="grid grid-cols-2 gap-4">
					<x-input
							label="Postal Code"
							placeholder="94103"
							wire:model="form.postal_code"
							class="w-full" />

					<x-input
							label="Country ISO"
							placeholder="US"
							wire:model="form.address_country_iso"
							class="w-full" />
				</div>

				<div class="grid grid-cols-2 gap-4">
					<x-input
							label="Latitude"
							placeholder="37.7749"
							wire:model="form.latitude"
							class="w-full" />

					<x-input
							label="Longitude"
							placeholder="-122.4194"
							wire:model="form.longitude"
							class="w-full" />
				</div>
			</div>
		</div>

		<!-- Navigation Buttons -->
		<div class="flex items-center justify-between gap-4 mt-8">
			<div>
				<!-- Left side empty for consistency with edit-subject.blade.php -->
			</div>

			<div class="flex items-center gap-4">
				<x-button
						sm
						wire:navigate.hover
						wire:click="cancel"
						color="secondary">
					Cancel
				</x-button>
				<x-button
						sm
						type="submit"
						primary>
					Update Contact Point
				</x-button>
			</div>
		</div>
	</form>
</div>

@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			// Get the subject ID from the backup field
			const subjectId = document.getElementById('subject_id_backup').value

			// Set the subject ID in the Livewire component directly
			if (subjectId) {
				try {
					// Try to find the Livewire component using Alpine's wireId
					const form = document.getElementById('contactPointForm')
					if (form && form._x_dataStack && form._x_dataStack[0] && form._x_dataStack[0].wireId) {
						window.livewire.find(form._x_dataStack[0].wireId).set('form.subject_id', subjectId)
						console.log('Subject ID set to:', subjectId)
					} else {
						// Fallback method if Alpine data is not available
						const wireId = form.getAttribute('wire:id')
						if (wireId) {
							window.livewire.find(wireId).set('form.subject_id', subjectId)
							console.log('Subject ID set using wire:id:', subjectId)
						} else {
							console.error('Could not find Livewire component ID')
						}
					}
				} catch (e) {
					console.error('Error setting subject_id in Livewire component:', e)
				}

				// Also set the value directly on the hidden input as a backup
				document.getElementById('subject_id_field').value = subjectId
			}

			// Add event listener for form submission
			document.getElementById('contactPointForm').addEventListener('submit', function (e) {
				// Get the subject ID again to ensure it's set
				const subjectId = document.getElementById('subject_id_backup').value

				if (subjectId) {
					try {
						// Try to find the Livewire component using Alpine's wireId
						const form = document.getElementById('contactPointForm')
						if (form && form._x_dataStack && form._x_dataStack[0] && form._x_dataStack[0].wireId) {
							window.livewire.find(form._x_dataStack[0].wireId).set('form.subject_id', subjectId)
							console.log('Subject ID set before submission:', subjectId)
						} else {
							// Fallback method if Alpine data is not available
							const wireId = form.getAttribute('wire:id')
							if (wireId) {
								window.livewire.find(wireId).set('form.subject_id', subjectId)
								console.log('Subject ID set before submission using wire:id:', subjectId)
							}
						}
					} catch (e) {
						console.error('Error setting subject_id before submission:', e)
					}

					// Also set the value directly on the hidden input as a backup
					document.getElementById('subject_id_field').value = subjectId
				}
			})
		})
	</script>
@endpush