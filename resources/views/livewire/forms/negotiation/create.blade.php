<?php

	use App\DTOs\NegotiationSubject\NegotiationSubjectDTO;
	use App\DTOs\NegotiationUser\NegotiationUserDTO;
	use App\DTOs\Subject\SubjectDTO;
	use App\Enums\Subject\MoodLevels;
	use App\Enums\Subject\SubjectNegotiationRoles;
	use App\Enums\Subject\SubjectNegotiationStatuses;
	use App\Enums\User\UserNegotiationRole;
	use App\Livewire\Forms\NegotiationForm;
	use App\Enums\Negotiation\NegotiationStatuses;
	use App\Enums\Negotiation\NegotiationTypes;
	use App\Models\ContactPoint;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\ContactPoint\ContactPointCreationService;
	use App\Services\MoodLog\MoodLogCreationService;
	use App\Services\Negotiation\NegotiationCreationService;
	use App\Services\NegotiationSubject\NegotiationSubjectCreationService;
	use App\Services\NegotiationUser\NegotiationUserCreationService;
	use App\Services\Subject\SubjectCreationService;
	use Illuminate\Foundation\Application;
	use Illuminate\Http\RedirectResponse;
	use Illuminate\Routing\Redirector;
	use Propaganistas\LaravelPhone\PhoneNumber;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Auth;

 new #[Layout('components.layouts.app')] class extends Component {
		public NegotiationForm $negotiationForm;
		public string $subjectName = '';
		public string $subjectPhone = '';

		public ?MoodLevels $current_mood = null;


		public function mount():void
		{
			// Set the tenant_id from the authenticated user's tenant
			$this->negotiationForm->tenant_id = Auth::user()->tenant_id;

			// Set the created_by field to the authenticated user's ID
			$this->negotiationForm->created_by = Auth::user()->id;

			// Set default status to active
			$this->negotiationForm->status = NegotiationStatuses::active->value;

			// Set default type to unknown
			$this->negotiationForm->type = NegotiationTypes::unknown->value;
		}

		/**
		 * Create a new subject based on form data
		 *
		 * @return Subject The newly created subject
		 */
		private function createSubject():Subject
		{
			// Create Subject
			$newSubjectDTO = new SubjectDTO(
				tenant_id: tenant()->id,
				name: $this->subjectName,
				current_mood: $this->current_mood,
				status: SubjectNegotiationStatuses::active,
				created_at: now(),
				updated_at: now()
			);

			// Create the Subject
			$subject = app(SubjectCreationService::class)->createSubject($newSubjectDTO);

			// Only create a contact point if a phone number is provided
			if (!empty($this->subjectPhone)) {
				$formattedPhone = new PhoneNumber($this->subjectPhone, 'US');

				// Create a ContactPoint record for the Subject with associated phone information
				app(ContactPointCreationService::class)->createContactPoint([
					'subject_id' => $subject->id,
					'tenant_id' => tenant()->id,
					'kind' => 'phone',
					'label' => 'home',
					'is_primary' => true,
					'is_verified' => false,
					'priority' => 1,
					'e164' => $formattedPhone,
					'ext' => null,
					'phone_country_iso' => 'US',
				]);
			}

			return $subject;
		}

		/**
		 * Create a negotiation subject link between negotiation and subject
		 *
		 * @param  int  $negotiationId  The negotiation ID
		 * @param  int  $subjectId  The subject ID
		 * @param  SubjectNegotiationRoles  $role  The role of the subject in the negotiation
		 *
		 * @return void
		 */
		private function createNegotiationSubject(
			int $negotiationId,
			int $subjectId,
			SubjectNegotiationRoles $role = SubjectNegotiationRoles::primary
		):void {
			$newNegotiationSubjectDTO = new NegotiationSubjectDTO(
				negotiation_id: $negotiationId,
				subject_id: $subjectId,
				role: $role,
			);

			app(NegotiationSubjectCreationService::class)
				->createNegotiationSubject($newNegotiationSubjectDTO);
		}

		/**
		 * Add the authenticated user to the negotiation
		 *
		 * @param  int  $negotiationId  The negotiation ID
		 *
		 * @return void
		 */
		private function addAuthUserToNegotiation(int $negotiationId):void
		{
			$negotiationUserDTO = new NegotiationUserDTO(
				negotiation_id: $negotiationId,
				user_id: Auth::user()->id,
				role: UserNegotiationRole::PrimaryNegotiator,
				status: 'active',
				joined_at: now(),
				left_at: null,
				created_at: now(),
				updated_at: now(),
			);
			
			app(NegotiationUserCreationService::class)
				->createNegotiationUser($negotiationUserDTO);
		}

		public function saveNegotiation():Redirector
		{
			$validated = $this->negotiationForm->validate();

			$newNegotiation = app(NegotiationCreationService::class)->createNegotiation($validated);

			$newSubject = $this->createSubject();

			$this->createNegotiationSubject(
				$newNegotiation->id,
				$newSubject->id,
				SubjectNegotiationRoles::primary
			);

			$selectedMood = $this->current_mood ?? MoodLevels::Neutral;
			$moodMetaData = [
				'createdByName' => authUser()->name,
				'subjectName' => $newSubject->name,
				'moodDescription' => $selectedMood->description(),
				'moodName' => $selectedMood->label()
			];

			$moodDto = new \App\DTOs\MoodLog\MoodLogDTO(
				id: null,
				tenant_id: tenant()->id,
				subject_id: $newSubject->id,
				logged_by_id: authUser()->id,
				negotiation_id: $newNegotiation->id,
				mood_level: $selectedMood->value,
				context: 'Created at new negotiation',
				meta_data: $moodMetaData,
				created_at: now(),
				updated_at: now()
			);

			app(MoodLogCreationService::class)->createMoodLog($moodDto);

			// Add the authenticated user to the negotiation
			$this->addAuthUserToNegotiation($newNegotiation->id);

			return redirect(route('negotiation-noc',
				['negotiation' => $newNegotiation->title, 'tenantSubdomain' => tenant()->subdomain]));
		}

		public function cancel()
		{
			return redirect(route('dashboard.negotiations', tenant()->subdomain));
		}
	}

?>

<div>
	<!-- Development-only button to fill form with random data -->
	@if(app()->environment('local'))
		<div class="mb-4">
			<x-button
					id="fillRandomDataBtn"
					type="button"
					color="secondary"
					class="mb-4">
				Fill with Random Data
			</x-button>
		</div>
	@endif

	<form
			id="createNegotiationForm"
			wire:submit.prevent="saveNegotiation"
			class="space-y-6">
		<!-- Basic Information -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Basic Information</h2>
			<p class="mb-4 text-sm text-gray-400">Enter the basic details about this negotiation</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-input
						icon="user"
						label="Title *"
						wire:model="negotiationForm.title"
						placeholder="Enter negotiation title" />

				<x-select.styled
						class="w-full"
						icon="flag"
						label="Status *"
						wire:model="negotiationForm.status"
						:options="collect(App\Enums\Negotiation\NegotiationStatuses::cases())->map(fn($status) => [
						'label' => $status->label(),
						'value' => $status->value,
					])->toArray()" />

				<x-select.styled
						class="w-full"
						icon="shield-exclamation"
						label="Type *"
						wire:model="negotiationForm.type"
						:options="collect(App\Enums\Negotiation\NegotiationTypes::cases())->map(fn($type) => [
						'label' => $type->label(),
						'value' => $type->value,
					])->toArray()" />

				<x-input
						icon="user"
						label="Subject Name *"
						wire:model="subjectName"
						placeholder="Enter subject name" />

				<x-input
						icon="phone"
						label="Phone"
						wire:model="subjectPhone"
						placeholder="Enter phone number" />

				<x-select.styled
						class="w-full"
						icon="user"
						label="Current Mood *"
						wire:model="current_mood"
						:options="collect(App\Enums\Subject\MoodLevels::cases())->map(fn($type) => [
						'label' => $type->label(),
						'value' => $type->value,
					])->toArray()" />


			</div>
		</div>

		<!-- Summary and Details -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Summary and Details</h2>
			<p class="mb-4 text-sm text-gray-400">Provide additional information about the negotiation</p>

			<div class="grid grid-cols-1 gap-4">
				<x-textarea
						label="Summary"
						wire:model="negotiationForm.summary"
						placeholder="Enter a brief summary of the negotiation"
						rows="3" />

				<x-textarea
						label="Initial Complaint"
						wire:model="negotiationForm.initial_complaint"
						placeholder="Describe the initial complaint or situation"
						rows="3" />

				<x-textarea
						label="Negotiation Strategy"
						wire:model="negotiationForm.negotiation_strategy"
						placeholder="Outline the strategy for this negotiation"
						rows="3" />
			</div>
		</div>

		<!-- Location Information -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold text-white">Location Information</h2>
			<p class="mb-4 text-sm text-gray-400">Enter details about where the negotiation took place</p>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-input
						icon="map-pin"
						label="Location Name"
						wire:model="negotiationForm.location"
						placeholder="Enter location name" />

				<x-input
						icon="home"
						label="Address"
						wire:model="negotiationForm.location_address"
						placeholder="Enter street address" />

				<x-input
						icon="building-office"
						label="City"
						wire:model="negotiationForm.location_city"
						placeholder="Enter city" />

				<x-input
						icon="map"
						label="State"
						wire:model="negotiationForm.location_state"
						placeholder="Enter state" />

				<x-input
						icon="hashtag"
						label="ZIP Code"
						type="number"
						wire:model="negotiationForm.location_zip"
						placeholder="Enter ZIP code" />

				<x-tag
						placeholder="Add tags"
						icon="tag"
						label="Tags"
						limit="4"
						wire:model="negotiationForm.tags" />
			</div>
		</div>

		<!-- Submit Button -->
		<div class="flex items-center justify-end gap-4">
			<x-button
					type="submit"
					primary>Create Negotiation
			</x-button>
			<x-button
					wire:click="cancel"
					color="secondary">
				Cancel
			</x-button>
		</div>
	</form>
</div>
@push('scripts')
	<script>
		// Prevent form submission on Enter key press
		document.getElementById('createNegotiationForm').addEventListener('keydown', function (event) {
			if (event.key === 'Enter') {
				event.preventDefault() // Stop the form from submitting
			}
		})

		// Fill form with random data for development purposes
		document.addEventListener('DOMContentLoaded', function () {
			const fillRandomDataBtn = document.getElementById('fillRandomDataBtn')
			if (fillRandomDataBtn) {
				fillRandomDataBtn.addEventListener('click', function () {
					// Helper functions for generating random data
					function getRandomInt (min, max) {
						return Math.floor(Math.random() * (max - min + 1)) + min
					}

					function getRandomElement (array) {
						return array[Math.floor(Math.random() * array.length)]
					}

					function getRandomString (length = 10) {
						const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
						let result = ''
						for (let i = 0; i < length; i++) {
							result += characters.charAt(Math.floor(Math.random() * characters.length))
						}
						return result
					}

					function getRandomSentence (wordCount = 8) {
						const words = [
							'negotiation', 'subject', 'situation', 'crisis', 'resolution', 'communication',
							'strategy', 'approach', 'team', 'response', 'incident', 'location', 'emergency',
							'protocol', 'procedure', 'assessment', 'evaluation', 'outcome', 'status', 'report',
							'hostage', 'barricaded', 'suicidal', 'training', 'scenario', 'simulation', 'exercise'
						]
						let sentence = ''
						for (let i = 0; i < wordCount; i++) {
							sentence += getRandomElement(words) + ' '
						}
						return sentence.trim().charAt(0).toUpperCase() + sentence.trim().slice(1)
					}

					function getRandomParagraph (sentenceCount = 3) {
						let paragraph = ''
						for (let i = 0; i < sentenceCount; i++) {
							paragraph += getRandomSentence(getRandomInt(5, 12)) + '. '
						}
						return paragraph.trim()
					}

					function getRandomPhoneNumber () {
						return `${getRandomInt(100, 999)}-${getRandomInt(100, 999)}-${getRandomInt(1000, 9999)}`
					}

					function getRandomName () {
						const firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Jessica', 'William', 'Jennifer']
						const lastNames = ['Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis', 'Miller', 'Wilson', 'Moore', 'Taylor']
						return `${getRandomElement(firstNames)} ${getRandomElement(lastNames)}`
					}

					function getRandomCity () {
						const cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'San Jose']
						return getRandomElement(cities)
					}

					function getRandomState () {
						const states = ['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY']
						return getRandomElement(states)
					}

					function getRandomAddress () {
						const streetTypes = ['St', 'Ave', 'Blvd', 'Dr', 'Ln', 'Rd', 'Way', 'Pl', 'Ct']
						return `${getRandomInt(100, 9999)} ${getRandomString(6).charAt(0).toUpperCase() + getRandomString(6).slice(1)} ${getRandomElement(streetTypes)}`
					}

					function getRandomZipCode () {
						return getRandomInt(10000, 99999)
					}

					function getRandomTags () {
						const possibleTags = ['urgent', 'high-risk', 'resolved', 'in-progress', 'standby', 'training', 'hostage', 'barricaded', 'suicidal', 'domestic', 'public', 'school', 'workplace', 'armed', 'unarmed']
						const tagCount = getRandomInt(1, 4)
						const tags = []

						for (let i = 0; i < tagCount; i++) {
							const tag = getRandomElement(possibleTags)
							if (!tags.includes(tag)) {
								tags.push(tag)
							}
						}

						return tags
					}

					// Get Livewire component instance
					const livewireComponent = window.Livewire.find(
						document.querySelector('[wire\\:id]').getAttribute('wire:id')
					)

					// Generate random data for each field
					const randomData = {
						'negotiationForm.title': `Negotiation ${getRandomString(4).toUpperCase()} - ${getRandomSentence(3)}`,
						'negotiationForm.status': getRandomElement(['active', 'resolved', 'failed', 'standby']),
						'negotiationForm.type': getRandomElement(['hostage', 'suicidal', 'barricaded', 'training', 'unknown']),
						'subjectName': getRandomName(),
						'subjectPhone': getRandomPhoneNumber(),
						'negotiationForm.current_mood': getRandomInt(1, 11).toString(),
						'negotiationForm.summary': getRandomParagraph(2),
						'negotiationForm.initial_complaint': getRandomParagraph(2),
						'negotiationForm.negotiation_strategy': getRandomParagraph(2),
						'negotiationForm.location': getRandomElement(['Police Station', 'Residence', 'School', 'Office Building', 'Shopping Mall', 'Hospital', 'Hotel', 'Public Park', 'Restaurant', 'Apartment Complex']),
						'negotiationForm.location_address': getRandomAddress(),
						'negotiationForm.location_city': getRandomCity(),
						'negotiationForm.location_state': getRandomState(),
						'negotiationForm.location_zip': getRandomZipCode(),
						'negotiationForm.tags': getRandomTags()
					}

					// Set the values in the Livewire component
					Object.entries(randomData).forEach(([key, value]) => {
						livewireComponent.set(key, value)
					})

					console.log('Form filled with random data')
				})
			}
		})
	</script>
@endpush