<?php

	use App\Services\Hostage\HostageDestructionService;
	use App\Services\Negotiation\NegotiationFetchingService;
	use Livewire\Volt\Component;
	use App\Models\Hostage;
	use Livewire\WithPagination;
	use TallStackUi\Traits\Interactions;

	new class extends Component {
		use WithPagination;
		use Interactions;

		public bool $showCreateHostageModal = false;
		public bool $showViewHostageModal = false;
		public bool $showEditHostageModal = false;
		public bool $showDeleteHostageModal = false;
		public $currentHostageId = null;
		public $hostages = [];
		public $negotiation;
		public string $sortBy = 'name';

		public function mount($negotiationId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)->getNegotiationById($negotiationId);
			$this->loadHostages();

		}

		public function loadHostages():void
		{
			$negotiation = $this->negotiation ?? null;

			if ($negotiation) {
				$hostages = Hostage::where('negotiation_id', $negotiation->id)
					->with(['images', 'contacts.phone', 'contacts.email'])
					->get();
			} else {
				$hostages = Hostage::with(['images', 'contacts.phone', 'contacts.email'])->get();
			}

			// Sort the hostages based on the sortBy property
			$this->hostages = $hostages->sortBy($this->sortBy);
		}

		/**
		 * Update the sort field and refresh the hostages
		 *
		 * @param  string  $field  The field to sort by
		 *
		 * @return void
		 */
		public function updateSort(string $field):void
		{
			$this->sortBy = $field;
			$this->loadHostages();
		}

		public function createHostage()
		{
			$negotiation = $this->negotiation ?? null;
			$tenant = tenant();

			if (!$tenant) {
				// Handle the case where tenant is null
				session()->flash('error', 'Unable to determine tenant. Please try again or contact support.');
				return;
			}

			if ($negotiation) {
				return $this->redirect(route('hostage.create', [
					'negotiation' => $negotiation,
					'tenantSubdomain' => $tenant->subdomain
				]));
			}

			return $this->redirect(route('hostage.create', [
				'tenantSubdomain' => $tenant->subdomain
			]));
		}


		public function viewHostage($hostageId)
		{
			$negotiation = $this->negotiation ?? null;
			$hostage = Hostage::find($hostageId);
			$tenant = tenant();

			if (!$tenant) {
				// Handle the case where tenant is null
				session()->flash('error', 'Unable to determine tenant. Please try again or contact support.');
				return;
			}

			if ($hostage) {
				return $this->redirect(route('hostage.show', [
					'hostage' => $hostage,
					'negotiation' => $negotiation,
					'tenantSubdomain' => $tenant->subdomain
				]));
			}
		}


		public function confirmDeleteHostage($hostageId):void
		{
			$this->currentHostageId = $hostageId;
			$this->showDeleteHostageModal = true;
		}

		public function deleteHostage():void
		{
			$this->showDeleteHostageModal = false;

			if ($this->currentHostageId) {
				$hostage = Hostage::find($this->currentHostageId);

				if ($hostage) {
					// Use the service to delete the hostage
					$hostageDestructionService = app(HostageDestructionService::class);
					$hostageDestructionService->deleteHostage($hostage->id);

					// Close the modal and reset currentHostageId
					$this->showDeleteHostageModal = false;
					$this->currentHostageId = null;

					// Reload hostages
					$this->loadHostages();
				}
			}
		}

		/**
		 * Define the event listeners for this component
		 *
		 * @return array Array of event listeners mapped to handler methods
		 */
		public function getListeners()
		{
			$tenantId = tenant()->id;
			$negotiationId = $this->negotiation->id;
			return [
				"echo-private:private.negotiation.$tenantId.$negotiationId,.HostageCreated" => 'handleHostageCreated',
				"echo-private:private.negotiation.$tenantId.$negotiationId,.HostageUpdated" => 'handleHostageUpdated',
				"echo-private:private.negotiation.$tenantId.$negotiationId,.HostageDestroyed" => 'handleHostageDestroyed',
			];
		}

		/**
		 * Handle the HostageCreated event by refreshing the hostages collection
		 *
		 * @param  array  $data  Event data
		 *
		 * @return void
		 */
		public function handleHostageCreated(array $data):void
		{
			$hostageId = $data['hostageId'] ?? $data['hostage'] ?? null;
			$hostage = $hostageId? app(\App\Services\Hostage\HostageFetchingService::class)->getHostage($hostageId) : null;

			if ($hostage) {
				$name = $hostage->name ?? 'a hostage';
				$message = "A new '{$name}' hostage was created.";
			} else {
				$message = "A hostage has been created.";
			}

			$this->toast()->timeout()->info($message)->send();
			$this->loadHostages();
		}

		/**
		 * Handle the HostageUpdated event by refreshing the hostages collection
		 *
		 * @param  array  $data  Event data
		 *
		 * @return void
		 */
		public function handleHostageUpdated(array $data):void
		{
			$hostageId = $data['hostageId'] ?? $data['hostage'] ?? null;
			$hostage = $hostageId? app(\App\Services\Hostage\HostageFetchingService::class)->getHostage($hostageId) : null;

			if ($hostage) {
				$name = $hostage->name ?? 'a hostage';
				$message = "Hostage '{$name}' was updated.";
			} else {
				$message = "A hostage has been updated.";
			}

			$this->toast()->timeout()->info($message)->send();
			$this->loadHostages();
		}

		/**
		 * Handle the HostageDestroyed event by refreshing the hostages collection
		 *
		 * @param  array  $data  Event data
		 *
		 * @return void
		 */
		public function handleHostageDestroyed(array $data):void
		{
			// Ensure any open delete modal is closed and internal state reset when a deletion event is received
			$this->showDeleteHostageModal = false;
			$this->currentHostageId = null;

			$details = $data['details'] ?? null;
			if ($details) {
				$hostageName = $details['hostageName'] ?? 'a hostage';
				$message = "Hostage '{$hostageName}' was deleted.";
			} else {
				$message = "A hostage has been deleted.";
			}
			$this->toast()->timeout()->info($message)->send();
			$this->loadHostages();
		}
	}

?>

<div x-data="{showHostages: true}">
	<div class="bg-teal-600 px-4 py-2 rounded-lg flex items-center justify-between">
		<h3 class="text-sm font-semibold">Hostages <span
					x-show="!showHostages"
					x-transition>({{ $negotiation->hostages->count() }})</span></h3>
		<div class="flex items-center gap-2">
			<select
					wire:model.live="sortBy"
					wire:change="updateSort($event.target.value)"
					class="text-xs py-1 pl-2 pr-7 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500">
				<option value="name">Sort by Name</option>
				<option value="age">Sort by Age</option>
				<option value="risk_level">Sort by Risk Level</option>
				<option value="last_seen_at">Sort by Last Seen</option>
			</select>
			<x-button
					wire:navigate.hover
					href="{{ route('hostage.create', [
						'negotiation' => $negotiation ?? null,
						'tenantSubdomain' => tenant()->subdomain
					]) }}"
					color="white"
					sm
					flat
					icon="plus" />
			<x-button
					@click="showHostages = !showHostages"
					color="white"
					sm
					flat
					icon="chevron-up-down" />
		</div>
	</div>
	<div
			class="mt-4 grid grid-cols-1 gap-4"
			x-show="showHostages"
			x-transition>

		@if(count($hostages) > 0)
			@foreach($hostages as $hostage)
				<div
						wire:key="tsui-card-{{ $hostage->id }}-{{ optional($hostage->updated_at)->timestamp }}">
					<x-card
							x-data="{
					currentImageIndex: 0,
					images: {{ json_encode($hostage->images->map->url()) }},
					totalImages: {{ $hostage->images->count() }},
					getCurrentImage() {
						return this.totalImages > 0
						? this.images[this.currentImageIndex]
						: `https://ui-avatars.com/api/?name=${encodeURIComponent('{{ $hostage->name }}')}`;
					},
					nextImage() {
						if (this.totalImages > 0) {
							this.currentImageIndex = (this.currentImageIndex + 1) % this.totalImages;
						}
					},
					previousImage() {
						if (this.totalImages > 0) {
							this.currentImageIndex = (this.currentImageIndex - 1 + this.totalImages) % this.totalImages;
						}
					}
				}">
						<div class="flex justify-between gap-4">
							<div>
								<div class="flex gap-4">
									<img
											class="size-26 rounded-lg"
											:src="getCurrentImage()"
											alt="Hostage Image">
									<div class="text-sm flex flex-col justify-between">
										<h5 class="font-semibold">{{ $hostage->name }}</h5>
										<p>{{ $hostage->age }} Year Old {{ $hostage->gender->label() }}</p>
										<p>{{ $hostage->contacts->where('kind', 'phone')->first()?->phone?->e164 ?? 'No phone' }}</p>
										<p>{{ $hostage->contacts->where('kind', 'email')->first()?->email?->email ?? 'No email' }}</p>
									</div>
								</div>
								<div
										class="mt-2 flex justify-between items-center w-fit"
										x-show="totalImages > 1">
									<button
											@click="previousImage"
											class="hover:bg-gray-200 dark:hover:bg-gray-700 hover:cursor-pointer p-1 rounded-lg"
									>
										<x-icon
												class="size-4"
												name="chevron-left" />
									</button>
									<p
											class="px-2 text-xs"
											x-text="`${currentImageIndex + 1} of ${totalImages}`"></p>
									<button
											@click="nextImage"
											class="hover:bg-gray-200 dark:hover:bg-gray-700 hover:cursor-pointer p-1 rounded-lg"
									>
										<x-icon
												class="size-4 hover:cursor-pointer"
												name="chevron-right" />
									</button>
								</div>
							</div>

							<div class="text-sm">
								<h5 class="font-semibold">Risk Factors</h5>
								@if($hostage->risk_factors && is_array($hostage->risk_factors))
									@foreach($hostage->risk_factors as $factor)
										<p>{{ $factor }}</p>
									@endforeach
								@else
									<p>No risk factors</p>
								@endif
							</div>
							<div class="text-sm">
								<h5 class="font-semibold">Details</h5>
								<p class="capitalize">
									<span class="italic">Relation:</span> {{ $hostage->relation_to_subject ?? 'Unknown Relation' }}
								</p>
								<p><span class="italic">Location:</span> {{ $hostage->location ?? 'Unknown Location' }}
								</p>
								<p>
									<span class="italic">Last Contact:</span> {{ $hostage->last_seen_at ? $hostage->last_seen_at->diffForHumans() : 'Unknown' }}
								</p>
							</div>
							<div class="flex flex-col items-center gap-2">
								@if($hostage->risk_level)
									<x-badge
											xs
											text="Risk: {{ $hostage->risk_level?->label() ?? 'Unknown' }}"
											round />
								@else
									<x-badge
											xs
											text="Risk: Unknown"
											round />
								@endif
								@if($hostage->injury_status)
									<x-badge
											xs
											color="{{ $hostage->injury_status === 'none' ? 'green' : 'red' }}"
											text="Injuries: {{ $hostage->injury_status->label() }}"
											round />
								@endif
								@if($hostage->status)
									<x-badge
											xs
											color="blue"
											text="Status: {{ $hostage->status }}"
											round />
								@endif
							</div>
							<div class="">
								<x-dropdown
										icon="ellipsis-vertical">
									<x-dropdown.items
											wire:click="viewHostage({{ $hostage->id }})"
											icon="eye"
											text="View" />
									<x-dropdown.items
											wire:navigate.hover
											href="{{ route('hostage.edit', [
											'hostage' => $hostage,
											'negotiation' => $negotiation ?? null,
											'tenantSubdomain' => tenant()->subdomain
										]) }}"
											icon="pencil-square"
											text="Edit" />
									<x-dropdown.items
											wire:click="confirmDeleteHostage({{ $hostage->id }})"
											icon="trash"
											text="Delete" />
								</x-dropdown>
							</div>
						</div>
					</x-card>
				</div>
			@endforeach
		@else
			<div class="col-span-3 text-center py-8">
				<p class="text-gray-500 mb-4">No Hostages at this time.</p>
				<p class="text-sm text-gray-400">Click the + button above to create a new hostage.</p>
			</div>
		@endif
	</div>

	<!-- Delete Confirmation Modal -->
	<template x-teleport="body">
		<x-modal
				persistent
				center
				wire="showDeleteHostageModal">
			<x-card title="Confirm Delete">
				<p class="mb-4">Are you sure you want to delete this hostage? This action cannot be undone.</p>

				<div class="flex justify-end space-x-2">
					<x-button
							wire:click="$toggle('showDeleteHostageModal')"
							color="secondary">Cancel
						</x-button>
					<x-button
							wire:click="deleteHostage"
							color="red">Delete
						</x-button>
				</div>
			</x-card>
		</x-modal>
	</template>
</div>
