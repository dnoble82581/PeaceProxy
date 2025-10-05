<?php

	use App\DTOs\NegotiationUser\NegotiationUserDTO;
	use App\Enums\Negotiation\NegotiationStatuses;
	use App\Enums\Negotiation\NegotiationTypes;
	use App\Enums\User\UserNegotiationRole;
	use App\Models\Negotiation;
	use App\Services\Negotiation\NegotiationFetchingService;
	use App\Services\NegotiationUser\NegotiationUserCreationService;
	use Illuminate\Database\Eloquent\Collection;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\Validate;
	use Livewire\Volt\Component;

	new #[Layout('layouts.app'), \Livewire\Attributes\Title('Negotiations - Peace Proxy')] class extends Component {
		public Collection $negotiations;
		public bool $roleModal = false;
		public bool $editModal = false;
		public bool $deleteModal = false;
		public int $selectedNegotiation;

		#[Validate('required')]
		public string $choseRole = '';

		#[Validate('required')]
		public string $title = '';

		public string $location = '';
		public string $location_address = '';
		public string $location_city = '';
		public string $location_state = '';
		public string $location_zip = '';

		#[Validate('required')]
		public string $status = '';

		#[Validate('required')]
		public string $type = '';

		public string $summary = '';
		public string $initial_complaint = '';
		public string $negotiation_strategy = '';
		public array $tags = [];

		public function mount():void
		{
			// Eager load any relationships that might be needed in the view
			$this->negotiations = app(NegotiationFetchingService::class)->fetchByTenant(authUser()->tenant_id);
		}

		public function openRoleModal(int $negotiationId):void
		{
			$this->selectedNegotiation = $negotiationId;
			$this->roleModal = true;
		}

		public function openEditModal(int $negotiationId):void
		{
			$this->selectedNegotiation = $negotiationId;

			// Find the negotiation in the collection
			$negotiation = $this->negotiations->firstWhere('id', $negotiationId);

			// Populate the form fields - basic information
			$this->title = $negotiation->title;
			$this->status = $negotiation->status->value; // Convert enum to string
			$this->type = $negotiation->type->value; // Convert enum to string

			// Populate summary and details
			$this->summary = $negotiation->summary ?? '';
			$this->initial_complaint = $negotiation->initial_complaint ?? '';
			$this->negotiation_strategy = $negotiation->negotiation_strategy ?? '';

			// Populate location information
			$this->location = $negotiation->location ?? '';
			$this->location_address = $negotiation->location_address ?? '';
			$this->location_city = $negotiation->location_city ?? '';
			$this->location_state = $negotiation->location_state ?? '';
			$this->location_zip = $negotiation->location_zip ?? '';
			$this->tags = $negotiation->tags ?? [];

			$this->editModal = true;
		}

		public function saveNegotiation():void
		{
			$this->validate([
				'title' => 'required',
				'status' => 'required',
				'type' => 'required',
				'location' => 'nullable',
				'location_address' => 'nullable',
				'location_city' => 'nullable',
				'location_state' => 'nullable',
				'location_zip' => 'nullable|numeric',
				'summary' => 'nullable',
				'initial_complaint' => 'nullable',
				'negotiation_strategy' => 'nullable',
				'tags' => 'nullable|array',
			]);

			// Find the negotiation in the database
			$negotiation = Negotiation::find($this->selectedNegotiation);

			// Update the negotiation
			$negotiation->update([
				'title' => $this->title,
				'location' => $this->location,
				'location_address' => $this->location_address ?? null,
				'location_city' => $this->location_city ?? null,
				'location_state' => $this->location_state ?? null,
				'location_zip' => $this->location_zip ?? null,
				'status' => NegotiationStatuses::from($this->status),
				'type' => NegotiationTypes::from($this->type),
				'summary' => $this->summary ?? null,
				'initial_complaint' => $this->initial_complaint ?? null,
				'negotiation_strategy' => $this->negotiation_strategy ?? null,
				'tags' => $this->tags ?? [],
			]);

			// Close the slide-over panel
			$this->editModal = false;

			// Refresh the negotiations list
			$this->negotiations = app(NegotiationFetchingService::class)->fetchByTenant(authUser()->tenant_id);
		}

		public function openDeleteModal(int $negotiationId):void
		{
			$this->selectedNegotiation = $negotiationId;
			$this->deleteModal = true;
		}

		public function deleteNegotiation():void
		{
			// Find the negotiation in the database
			$negotiation = Negotiation::find($this->selectedNegotiation);

			// Delete the negotiation
			$negotiation->delete();

			// Close the modal
			$this->deleteModal = false;

			// Refresh the negotiations list
			$this->negotiations = app(NegotiationFetchingService::class)->fetchByTenant(authUser()->tenant_id);
		}

		public function enterNegotiation(int $negotiationId):void
		{
			$this->validateOnly('choseRole', ['choseRole' => 'required|string']);

			// Find the negotiation in the database
			$negotiation = Negotiation::find($negotiationId);

			// Update started_at if it's empty
			if ($negotiation && is_null($negotiation->started_at)) {
				$negotiation->update([
					'started_at' => now()
				]);
			}

			$this->addUserToNegotiation($negotiationId);

			// Get the negotiation title directly from the collection to avoid an extra query
			$negotiationData = $this->negotiations->firstWhere('id', $negotiationId);
			$title = $negotiationData? $negotiationData->title : '';

			// Redirect based on selected role
			if (in_array($this->choseRole, [
				\App\Enums\User\UserNegotiationRole::TacticalUser->value,
				\App\Enums\User\UserNegotiationRole::TacticalCommander->value,
			])) {
				$this->redirect(route('negotiation.tactical-noc', [
					'tenantSubdomain' => tenant()->subdomain,
					'negotiation' => $title
				]));
				return;
			}

			$this->redirect(route('noc', tenant()->subdomain));

//			$this->redirect(route('negotiation-noc', [
//				'tenantSubdomain' => tenant()->subdomain,
//				'negotiation' => $title
//			]));
		}

		private function addUserToNegotiation(int $negotiationId):void
		{
			// Use a transaction to ensure both operations complete successfully
			\Illuminate\Support\Facades\DB::transaction(function () use ($negotiationId) {
				// Update left_at for all previous records of this user in this negotiation
				// This ensures we track each time a user enters a negotiation, regardless of role
				\Illuminate\Support\Facades\DB::table('negotiation_users')
					->where('negotiation_id', $negotiationId)
					->where('user_id', authUser()->id)
					->whereNull('left_at')
					->update([
						'left_at' => now(),
						'updated_at' => now(),
					]);

				// Create a new record with the chosen role
				$negotiationUserDTO = new NegotiationUserDTO(
					negotiation_id: $negotiationId, user_id: authUser()->id,
					role: UserNegotiationRole::from($this->choseRole), status: 'active',
					joined_at: now(), left_at: null,
					created_at: now(), updated_at: now(),
				);
				app(NegotiationUserCreationService::class)
					->createNegotiationUser($negotiationUserDTO);
			});
		}
	}

?>

<div class="">
	<div class="">
		<div class="mx-auto sm:px-4 lg:px-2">
			<div class="bg-white dark:bg-dark-700 overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900 dark:text-white">
					@if($negotiations->isEmpty())
						<div class="text-center py-8">
							<p class="text-gray-500 dark:text-white">No negotiations found.</p>
							<p class="mt-2">Create your first negotiation to get started.</p>
							<div class="mt-4">
								<x-button
										wire:navigate
										href="{{route('negotiation.create', tenant()->subdomain)}}"
										sm>Create Negotiation
								</x-button>
							</div>
						</div>
					@else
						<div class="overflow-x-auto">
							<div class="px-4 sm:px-6 lg:px-8">
								<div class="flex items-center justify-between">
									<div class="sm:flex-auto">
										<h1 class="text-base font-semibold text-gray-900 dark:text-white">
											Negotiations</h1>
										<p class="mt-2 text-sm text-gray-700 dark:text-white">A list of your agencies
										                                                      negotiations</p>
									</div>
									<div class="flex-none">
										<x-button
												wire:navigate
												href="{{route('negotiation.create', tenant()->subdomain)}}"
												sm>Create Negotiation
										</x-button>
									</div>
								</div>
								<div class="mt-8 flow-root">
									<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
										<div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
											<table class="min-w-full divide-y divide-gray-300">
												<thead>
												<tr>
													<th
															scope="col"
															class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-0">
														Title
													</th>
													<th
															scope="col"
															class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
														Location
													</th>
													<th
															scope="col"
															class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
														Status
													</th>
													<th
															scope="col"
															class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
														Type
													</th>
													<th
															scope="col"
															class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
														Created By
													</th>
													<th
															scope="col"
															class="relative py-3.5 pr-4 pl-3 sm:pr-0">
														<span class="sr-only">Edit</span>
													</th>
												</tr>
												</thead>
												<tbody class="divide-y divide-gray-200">
												@foreach($negotiations as $negotiation)
													<tr>
														<td class="py-4 pr-3 pl-4 text-xs font-medium whitespace-nowrap text-gray-900 dark:text-white sm:pl-0">
															{{ $negotiation->title }}
														</td>
														<td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-white">
															{{ $negotiation->location ?? 'No Location Recorded' }}
														</td>
														<td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-white">
															{{ $negotiation->status->label() }}
														</td>
														<td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-white">
															{{ $negotiation->type->label() }}
														</td>
														<td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-white">
															{{ $negotiation->creator ? $negotiation->creator->name : 'Unknown' }}
														</td>
														<td class="relative py-4 pr-4 pl-3 text-right text-xs font-medium whitespace-nowrap sm:pr-0">
															<x-button
																	wire:click="openEditModal({{ $negotiation->id }})"
																	color="sky"
																	flat
																	icon="pencil-square" />
															<x-button
																	wire:click="openDeleteModal({{ $negotiation->id }})"
																	color="rose"
																	flat
																	icon="x-mark" />
															<x-button
																	wire:click="openRoleModal({{ $negotiation->id }})"
																	color="teal"
																	flat
																	icon="arrow-left-end-on-rectangle" />
															<x-modal
																	persistent
																	center
																	wire="roleModal"
																	title="Choose Your Role">
																{{--																<p>Choose your role in this negotiation</p>--}}
																<div class="mb-4">
																	<label
																			class="block mb-2"
																			for="chooseRole">Choose your role in this
																	                         negotiation</label>
																	<select
																			class="text-black w-full dark:text-dark-300 dark:bg-dark-700 rounded-md text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
																			placeholder="test"
																			wire:model="choseRole"
																			id="chooseRole"
																			required
																			class="mt-2">
																		@if(empty($choseRole))
																			<option
																					value=""
																					selected>Select Your Role...
																			</option>
																		@else
																			<option value="">Make Selection</option>
																		@endif
																		@foreach(\App\Enums\User\UserNegotiationRole::cases() as $role)
																			<option value="{{ $role->value }}">{{ $role->label() }}</option>
																		@endforeach
																	</select>
																	@error('choseRole')
																	<p class="text-rose-500">{{ $message }}</p>
																	@enderror
																</div>
																<div class="space-x-2">
																	<x-button wire:click="enterNegotiation({{ $selectedNegotiation }})">
																		Enter
																	</x-button>
																	<x-button
																			wire:click="$toggle('roleModal')"
																			color="zinc">Cancel
																	</x-button>
																</div>


															</x-modal>

															<!-- Edit Negotiation Slide -->
															<x-slide
																	size="4xl"
																	persistent
																	position="right"
																	wire="editModal"
																	title="Edit Negotiation">
																<div class="mt-4 space-y-6 overflow-y-auto h-full pb-20 px-8">
																	<!-- Basic Information -->
																	<div class="mb-6">
																		<h2 class="text-lg font-semibold text-dark-500 dark:text-dark-100">
																			Basic Information</h2>
																		<p class="mb-4 text-sm text-gray-500 dark:text-dark-300">
																			Edit the
																			basic
																			details
																			about this
																			negotiation</p>

																		<div class="grid grid-cols-1 gap-4">
																			<div>
																				<x-input
																						icon="user"
																						label="Title *"
																						wire:model="title"
																						placeholder="Enter negotiation title"
																						required />
																			</div>
																			<div>
																				<x-select.styled
																						class="w-full"
																						icon="flag"
																						label="Status *"
																						wire:model="status"
																						placeholder="Enter status"
																						:options="collect(App\Enums\Negotiation\NegotiationStatuses::cases())->map(fn($status) => [
																						'label' => $status->label(),
																						'value' => $status->value,
																						])->toArray()"
																						required />
																			</div>
																			<div>
																				<x-select.styled
																						class="w-full"
																						icon="shield-exclamation"
																						label="Type *"
																						wire:model="type"
																						placeholder="Enter type"
																						:options="collect(App\Enums\Negotiation\NegotiationTypes::cases())->map(fn($type) => [
																						'label' => $type->label(),
																						'value' => $type->value,
																						])->toArray()"
																						required />
																			</div>
																		</div>
																	</div>

																	<!-- Summary and Details -->
																	<div class="mb-6">
																		<h2 class="text-lg font-semibold text-dark-500 dark:text-dark-100">
																			Summary and Details</h2>
																		<p class="mb-4 text-sm text-gray-500 dark:text-dark-300">
																			Provide
																			additional
																			information
																			about the
																			negotiation</p>

																		<div class="grid grid-cols-1 gap-4">
																			<x-textarea
																					label="Summary"
																					wire:model="summary"
																					placeholder="Enter a brief summary of the negotiation"
																					rows="3" />

																			<x-textarea
																					label="Initial Complaint"
																					wire:model="initial_complaint"
																					placeholder="Describe the initial complaint or situation"
																					rows="3" />

																			<x-textarea
																					label="Negotiation Strategy"
																					wire:model="negotiation_strategy"
																					placeholder="Outline the strategy for this negotiation"
																					rows="3" />
																		</div>
																	</div>

																	<!-- Location Information -->
																	<div class="mb-6">
																		<h2 class="text-lg font-semibold text-dark-500 dark:text-dark-100">
																			Location Information</h2>
																		<p class="mb-4 text-sm text-gray-500 dark:text-dark-300">
																			Enter
																			details
																			about
																			where the
																			negotiation
																			took
																			place</p>

																		<div class="grid grid-cols-1 gap-4">
																			<x-input
																					icon="map-pin"
																					label="Location Name"
																					wire:model="location"
																					placeholder="Enter location name" />

																			<x-input
																					icon="home"
																					label="Address"
																					wire:model="location_address"
																					placeholder="Enter street address" />

																			<div class="grid grid-cols-2 gap-4">
																				<x-input
																						icon="building-office"
																						label="City"
																						wire:model="location_city"
																						placeholder="Enter city" />

																				<x-input
																						icon="map"
																						label="State"
																						wire:model="location_state"
																						placeholder="Enter state" />
																			</div>

																			<x-input
																					icon="hashtag"
																					label="ZIP Code"
																					wire:model="location_zip"
																					placeholder="Enter ZIP code" />

																			<x-tag
																					hint="Add tag and then commit it by pressing enter"
																					placeholder="Add tags"
																					icon="tag"
																					label="Tags"
																					limit="4"
																					wire:model="tags" />
																		</div>
																	</div>

																	<!-- Action Buttons -->

																	<x-slot:footer end>
																		<div class="space-x-2 bg-dark-200 dark:bg-dark-800 w-full p-4">
																			<x-button
																					wire:click="$toggle('editModal')"
																					color="zinc">
																				Cancel
																			</x-button>
																			<x-button
																					wire:click="saveNegotiation"
																					primary>
																				Save Changes
																			</x-button>
																		</div>
																	</x-slot:footer>
															</x-slide>

															<!-- Delete Confirmation Modal -->
															<x-modal
																	persistent
																	center
																	wire="deleteModal"
																	title="Delete Negotiation">
																<p>Are you sure you want to delete this negotiation?
																   This action cannot be undone.</p>
																<div class="mt-4 space-x-2">
																	<x-button
																			wire:click="deleteNegotiation"
																			color="rose">
																		Delete
																	</x-button>
																	<x-button
																			wire:click="$toggle('deleteModal')"
																			color="zinc">Cancel
																	</x-button>
																</div>
															</x-modal>
														</td>
													</tr>
												@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>