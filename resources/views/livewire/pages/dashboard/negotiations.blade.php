<?php

	use App\DTOs\NegotiationUser\NegotiationUserDTO;
	use App\Enums\User\UserNegotiationRole;
	use App\Models\Negotiation;
	use App\Services\Negotiation\NegotiationFetchingService;
	use App\Services\NegotiationUser\NegotiationUserCreationService;
	use Illuminate\Database\Eloquent\Collection;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\Validate;
	use Livewire\Volt\Component;

	new #[Layout('layouts.app')] class extends Component {
		public Collection $negotiations;
		public bool $roleModal = false;
		public int $selectedNegotiation;

		#[Validate('required')]
		public string $choseRole = '';

		public function mount():void
		{
			$this->negotiations = app(NegotiationFetchingService::class)->fetchByTenant(authUser()->tenant_id);
		}

		public function openRoleModal(int $negotiationId):void
		{
			$this->selectedNegotiation = $negotiationId;
			$this->roleModal = true;
		}

		public function enterNegotiation(int $negotiationId):void
		{
			$this->validate();

			$this->addUserToNegotiation($negotiationId);

			$this->redirect(route('negotiation-noc', [
				'tenantSubdomain' => tenant()->subdomain,
				'negotiation' => Negotiation::find($negotiationId)->title
			]));
		}

		private function addUserToNegotiation(int $negotiationId):void
		{
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
		}
	}

?>

<div>
	<div class="">
		<div class="mx-auto sm:px-4 lg:px-2">
			<div class="bg-white dark:bg-dark-700 overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900 dark:text-gray-100">
					<div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
						<x-button
								wire:navigate
								href="{{route('negotiation.create', tenant()->subdomain)}}"
								sm>Create Negotiation
						</x-button>
					</div>
					@if($negotiations->isEmpty())
						<div class="text-center py-8">
							<p class="text-gray-500 dark:text-gray-400">No negotiations found.</p>
							<p class="mt-2">Create your first negotiation to get started.</p>
						</div>
					@else
						<div class="overflow-x-auto">
							<div class="px-4 sm:px-6 lg:px-8">
								<div class="sm:flex sm:items-center">
									<div class="sm:flex-auto">
										<h1 class="text-base font-semibold text-gray-900 dark:text-white">
											Negotiations</h1>
										<p class="mt-2 text-sm text-gray-700 dark:text-white">A list of your agencies
										                                                      negotiations</p>
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
															{{ $negotiation->status }}
														</td>
														<td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-white">
															{{ $negotiation->type }}
														</td>
														<td class="relative py-4 pr-4 pl-3 text-right text-xs font-medium whitespace-nowrap sm:pr-0">
															<x-button
																	color="sky"
																	flat

																	icon="pencil-square" />
															<x-button
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
																<p>Choose your role in this negotiation</p>
																<div class="mt-4 space-y-4">
																	<x-select.styled
																			placeholder="Select Role..."
																			wire:model="choseRole"
																			:request="route('enums.user-negotiation-roles')"
																	/>
																	<div class="space-x-2">
																		<x-button wire:click="enterNegotiation({{ $selectedNegotiation }})">
																			Enter
																		</x-button>
																		<x-button
																				wire:click="$toggle('roleModal')"
																				color="zinc">cancel
																		</x-button>
																	</div>

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