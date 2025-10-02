<?php

	use App\Enums\Tenant\TenantTypes;
	use App\Models\Tenant;
	use App\Models\User;
	use App\Services\Image\ImageService;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Validation\Rules;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Livewire\WithFileUploads;

	new #[Layout('layouts.app'), \Livewire\Attributes\Title('Settings - Peace Proxy')] class extends Component {
		use WithFileUploads;

		// Tenant settings properties
		public $tenant;
		public $agencyName;
		public $agencyEmail;
		public $agencyPhone;
		public $agencyWebsite;
		public $agencyType;
		public $primaryColor;
		public $secondaryColor;
		public $logo = null;
		public $addressLine1;
		public $addressLine2;
		public $addressCity;
		public $addressState;
		public $addressPostal;
		public $addressCountry;
		public $billingEmail;
		public $billingPhone;
		public $tax_id;
		public $agency_identifier;
		public $federal_agency_code;

		// User settings properties
		public User $user;
		public $userName;
		public $userEmail;
		public $permissions;
		public $rank_or_title;
		public $badge_number;
		public $license_number;
		public $department;
		public $phone;
		public ?string $password = null;
		public ?string $password_confirmation = null;
		public $avatar = null;

		// UI state
		public $activeTab = 'agency'; // 'agency', 'profile', or 'billing'

		public function mount()
		{
			// Check if tab parameter is present in the request

			if (authUser()->cannot('update', tenant())) {
				$this->activeTab = 'profile';
			}

			// Initialize tenant settings
			$this->tenant = auth()->user()->tenant;
			$this->agencyName = $this->tenant->agency_name;
			$this->agencyEmail = $this->tenant->agency_email;
			$this->agencyPhone = $this->tenant->agency_phone;
			$this->agencyWebsite = $this->tenant->agency_website;
			$this->agencyType = $this->tenant->agency_type;
			$this->primaryColor = $this->tenant->primary_color;
			$this->secondaryColor = $this->tenant->secondary_color;
			$this->addressLine1 = $this->tenant->address_line1;
			$this->addressLine2 = $this->tenant->address_line2;
			$this->addressCity = $this->tenant->address_city;
			$this->addressState = $this->tenant->address_state;
			$this->addressPostal = $this->tenant->address_postal;
			$this->addressCountry = $this->tenant->address_country;
			$this->billingEmail = $this->tenant->billing_email;
			$this->billingPhone = $this->tenant->billing_phone;
			$this->tax_id = $this->tenant->tax_id;
			$this->agency_identifier = $this->tenant->agency_identifier;
			$this->federal_agency_code = $this->tenant->federal_agency_code;

			// Initialize user settings
			$this->user = Auth::user();
			$this->userName = $this->user->name;
			$this->userEmail = $this->user->email;
			$this->permissions = $this->user->permissions;
			$this->rank_or_title = $this->user->rank_or_title;
			$this->badge_number = $this->user->badge_number;
			$this->license_number = $this->user->license_number;
			$this->department = $this->user->department;
			$this->phone = $this->user->phone;
		}

		public function setActiveTab($tab):void
		{
			$this->activeTab = $tab;
		}

		public function updateAgencySettings():void
		{
			$this->validate([
				'agencyName' => 'required|string|max:255',
				'agencyEmail' => 'required|email|max:255',
				'agencyPhone' => 'nullable|string|max:20',
				'agencyWebsite' => 'nullable|url|max:255',
				'agencyType' => 'required|string|in:'.implode(',', array_column(TenantTypes::toArray(), 'value')),
				'primaryColor' => 'nullable|string|max:20',
				'secondaryColor' => 'nullable|string|max:20',
				'logo' => 'nullable|image|max:1024', // 1MB max
				'addressLine1' => 'nullable|string|max:255',
				'addressLine2' => 'nullable|string|max:255',
				'addressCity' => 'nullable|string|max:255',
				'addressState' => 'nullable|string|max:255',
				'addressPostal' => 'nullable|string|max:255',
				'addressCountry' => 'nullable|string|max:2',
				'billingEmail' => 'nullable|email|max:255',
				'billingPhone' => 'nullable|string|max:20',
				'tax_id' => 'nullable|string|max:255',
				'agency_identifier' => 'nullable|string|max:255',
				'federal_agency_code' => 'nullable|string|max:255',
			]);

			$this->tenant->update([
				'agency_name' => $this->agencyName,
				'agency_email' => $this->agencyEmail,
				'agency_phone' => $this->agencyPhone,
				'agency_website' => $this->agencyWebsite,
				'agency_type' => $this->agencyType,
				'primary_color' => $this->primaryColor,
				'secondary_color' => $this->secondaryColor,
				'address_line1' => $this->addressLine1,
				'address_line2' => $this->addressLine2,
				'address_city' => $this->addressCity,
				'address_state' => $this->addressState,
				'address_postal' => $this->addressPostal,
				'address_country' => $this->addressCountry,
				'billing_email' => $this->billingEmail,
				'billing_phone' => $this->billingPhone,
				'tax_id' => $this->tax_id,
				'agency_identifier' => $this->agency_identifier,
				'federal_agency_code' => $this->federal_agency_code,
			]);

			// Handle logo upload if provided
			if ($this->logo) {
				$imageService = app(ImageService::class);

				// Delete existing logo images if any
				$existingImages = $this->tenant->images()->get();
				foreach ($existingImages as $image) {
					$imageService->deleteImage($image);
				}

				// Upload new logo
				$images = $imageService->uploadImagesForModel(
					[$this->logo],
					$this->tenant,
					'tenants',
					's3_public'
				);

				// Set the first image as primary
				if (count($images) > 0) {
					$imageService->setPrimaryImage($images[0]);

					// Update tenant's logo_path
					$this->tenant->logo_path = $images[0]->url();
					$this->tenant->save();
				}
			}

			// Reset form fields
			$this->reset(['logo']);

			session()->flash('message', 'Agency settings updated successfully.');
		}

		public function updateUserProfile():void
		{
			$this->validate([
				'userName' => ['required', 'string', 'max:255'],
				'userEmail' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
				'permissions' => ['nullable', 'string', 'max:255'],
				'rank_or_title' => ['nullable', 'string', 'max:255'],
				'badge_number' => ['nullable', 'string', 'max:255'],
				'license_number' => ['nullable', 'string', 'max:255'],
				'department' => ['nullable', 'string', 'max:255'],
				'phone' => ['nullable', 'string', 'max:255'],
				'password' => ['nullable', 'string', 'confirmed', Rules\Password::defaults()],
				'avatar' => ['nullable', 'image', 'max:1024'], // 1MB max
			]);

			// Update user properties
			$this->user->name = $this->userName;
			$this->user->email = $this->userEmail;
			$this->user->permissions = $this->permissions;
			$this->user->rank_or_title = $this->rank_or_title;
			$this->user->badge_number = $this->badge_number;
			$this->user->license_number = $this->license_number;
			$this->user->department = $this->department;
			$this->user->phone = $this->phone;

			// Update password if provided
			if ($this->password) {
				$this->user->password = Hash::make($this->password);
			}

			// Save user changes
			$this->user->save();

			// Handle avatar upload if provided
			if ($this->avatar) {
				$imageService = app(ImageService::class);

				// Delete existing avatar images if any
				$existingImages = $this->user->images()->get();
				foreach ($existingImages as $image) {
					$imageService->deleteImage($image);
				}

				// Upload new avatar
				$images = $imageService->uploadImagesForModel(
					[$this->avatar],
					$this->user,
					'users',
					's3_public'
				);

				// Set the first image as primary
				if (count($images) > 0) {
					$imageService->setPrimaryImage($images[0]);

					// Update user's avatar_path
					$this->user->avatar_path = $images[0]->url();
					$this->user->save();
				}
			}

			// Reset form fields
			$this->reset(['password', 'password_confirmation', 'avatar']);

			// Refresh user properties
			$this->userName = $this->user->name;
			$this->userEmail = $this->user->email;

			// Show success message
			session()->flash('message', 'Profile updated successfully.');
		}

		#[\Livewire\Attributes\On('userSubscribed')]
		public function userSubscribed()
		{
			$this->dispatch('refresh');
		}

		public function cancel()
		{
			// Redirect to the dashboard
			return redirect()->route('dashboard', ['tenantSubdomain' => tenant()->subdomain]);
		}
	}

?>

<div>
	<div class="">
		<div class="mx-auto sm:px-4 lg:px-6">
			<div class="bg-white dark:bg-dark-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-300 dark:border-dark-600">
				<div class="p-6 text-dark-800 dark:text-white">
					<h4 class="text-lg p-2 bg-gray-200/50 dark:text-white dark:bg-dark-800/50 rounded-t-lg mb-4">
						Settings</h4>
					@if (session('message'))
						<div
								x-data="{ show: true }"
								x-init="setTimeout(() => { show = false }, 3000)"
								x-show="show"
								x-transition:leave="transition ease-in duration-300"
								x-transition:leave-start="opacity-100"
								x-transition:leave-end="opacity-0"
								class="mb-4 p-4 bg-green-100 text-green-700 rounded">
							{{ session('message') }}
						</div>
					@endif

					<!-- Tabs -->
					<div class="mb-6 border-b border-gray-200">
						<ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
							<li class="mr-2">
								@can('update', tenant())
									<button
											wire:click="setActiveTab('agency')"
											class="inline-block p-4 {{ $activeTab === 'agency' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
									>
										Agency Settings
									</button>
								@endcan
							</li>
							<li class="mr-2">
								<button
										wire:click="setActiveTab('profile')"
										class="inline-block p-4 {{ $activeTab === 'profile' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
								>
									Profile Settings
								</button>
							</li>
							{{--							<li class="mr-2">--}}
							{{--								@can('update', tenant())--}}
							{{--									<button--}}
							{{--											wire:click="setActiveTab('billing')"--}}
							{{--											class="inline-block p-4 {{ $activeTab === 'billing' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"--}}
							{{--									>--}}
							{{--										Billing--}}
							{{--									</button>--}}
							{{--								@endcan--}}
							{{--							</li>--}}
						</ul>
					</div>

					<!-- Agency Settings Tab -->
					@can('update', tenant())
						<div class="{{ $activeTab === 'agency' ? 'block' : 'hidden' }}">
							<form
									wire:submit.prevent="updateAgencySettings"
									x-data="{ billing: false, identifiers: false }">
								<div
										class="grid grid-cols-1 md:grid-cols-2 gap-6">
									<!-- Agency Information -->
									<div class="col-span-1 md:col-span-2">
										<h2 class="text-lg mb-4 font-semibold">Agency Information</h2>
									</div>

									<div>
										<x-input
												label="Agency Name *"
												wire:model="agencyName"
										/>
									</div>

									<div>
										<x-input
												label="Agency Email *"
												wire:model="agencyEmail"
										/>
									</div>

									<div>
										<x-input
												label="Agency Phone"
												wire:model="agencyPhone"
										/>
									</div>

									<div>
										<x-input
												label="Agency Website"
												wire:model="agencyWebsite" />
									</div>

									<div>
										<x-select.styled
												label="Agency Type *"
												id="agencyType"
												wire:model="agencyType"
												class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
												:options="collect(\App\Enums\Tenant\TenantTypes::cases())->map(fn($type) => [
																				'label' => $type->label(),
																				'value' => $type->value,
																				])->toArray()"
										/>
									</div>

									<!-- Billing Address -->
									<div class="col-span-1 md:col-span-2 mt-6">
										<div class="flex justify-between items-center mb-4 border-b pb-2">
											<h2 class="text-lg font-medium">Billing Information</h2>
											<button
													type="button"
													@click="billing = !billing"
													class="px-3 py-1 text-sm rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-200"
											>
												<span x-text="billing ? 'Hide Address' : 'Show Address'"></span>
											</button>
										</div>
									</div>
									<div
											class="grid grid-cols-1 md:grid-cols-2 col-span-full gap-6"
											x-show="billing"
											x-transition:enter="transition ease-out duration-300"
											x-transition:enter-start="opacity-0 transform scale-95"
											x-transition:enter-end="opacity-100 transform scale-100"
											x-transition:leave="transition ease-in duration-200"
											x-transition:leave-start="opacity-100 transform scale-100"
											x-transition:leave-end="opacity-0 transform scale-95">
										<div>
											<x-input
													icon="envelope"
													label="Billing Email"
													type="email"
													wire:model="billingEmail"
											/>
										</div>

										<div>
											<x-input
													icon="phone"
													label="Billing Phone"
													wire:model="billingPhone"
											/>
										</div>

										<div>
											<x-input
													label="Address Line 1"
													wire:model="addressLine1"
											/>
										</div>

										<div>
											<x-input
													label="Address Line 2"
													wire:model="addressLine2"
											/>
										</div>

										<div>
											<x-input
													label="City"
													wire:model="addressCity"
											/>
										</div>

										<div>
											<x-input
													label="State/Province"
													wire:model="addressState"
											/>
										</div>

										<div>
											<x-input
													label="Postal/ZIP Code"
													wire:model="addressPostal"
											/>
										</div>

										<div>
											<x-input
													label="Country Code (2 letters)"
													wire:model="addressCountry"
											/>
										</div>
									</div>

									<!-- Agency Identifiers -->
									<div class="col-span-1 md:col-span-2 mt-6">
										<div class="flex justify-between items-center mb-4 border-b pb-2">
											<h2 class="text-lg font-medium">Agency Identifiers</h2>
											<button
													type="button"
													@click="identifiers = !identifiers"
													class="px-3 py-1 text-sm rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-200"
											>
												<span x-text="identifiers ? 'Hide Identifiers' : 'Show Identifiers'"></span>
											</button>
										</div>
									</div>
									<div
											class="grid grid-cols-1 md:grid-cols-2 col-span-full gap-6"
											x-show="identifiers"
											x-transition:enter="transition ease-out duration-300"
											x-transition:enter-start="opacity-0 transform scale-95"
											x-transition:enter-end="opacity-100 transform scale-100"
											x-transition:leave="transition ease-in duration-200"
											x-transition:leave-start="opacity-100 transform scale-100"
											x-transition:leave-end="opacity-0 transform scale-95">
										<div>
											<x-input
													label="Tax ID"
													wire:model="tax_id"
											/>
										</div>

										<div>
											<x-input
													label="Agency Identifier"
													wire:model="agency_identifier"
											/>
										</div>

										<div>
											<x-input
													label="Federal Agency Code"
													wire:model="federal_agency_code"
											/>
										</div>
									</div>

									<!-- Logo Upload -->
									<div class="col-span-1 md:col-span-2 mt-6">
										<h2 class="text-lg font-medium mb-4 border-b pb-2">Agency Logo</h2>
										<div class="flex items-center">
											<div class="mr-4">
												@if ($logo)
													<img
															src="{{ $logo->temporaryUrl() }}"
															alt="Logo Preview"
															class="w-32 h-32 object-contain">
												@elseif ($tenant->logo_path)
													<img
															src="{{ $tenant->logo_path }}"
															alt="{{ $tenant->agency_name }} Logo"
															class="w-32 h-32 object-contain">
												@else
													<div class="w-32 h-32 flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-400 border border-gray-300 dark:border-gray-600">
														<span>No Logo</span>
													</div>
												@endif
											</div>
											<div>
												<x-upload
														type="file"
														wire:model="logo"
														id="logo"
														class="mt-1 block w-full"
														accept="image/*" />
												<p class="text-sm text-gray-500 mt-1">Upload a new agency logo (max
												                                      1MB).</p>
												@error('logo')
												<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
											</div>
										</div>
									</div>

									<!-- Appearance Settings -->
									<div class="col-span-1 md:col-span-2 mt-6">
										<h2 class="text-lg font-medium mb-4 border-b pb-2">Appearance Settings</h2>
									</div>

									<div>
										<label
												for="primaryColor"
												class="block text-sm font-medium text-dark-800 dark:text-gray-100">Primary
										                                                                           Color</label>
										<div class="flex items-center mt-1">
											<input
													type="color"
													id="primaryColor"
													wire:model="primaryColor"
													class="h-8 w-8 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
											<input
													type="text"
													wire:model="primaryColor"
													class="ml-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
										</div>
										@error('primaryColor')
										<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
									</div>

									<div>
										<label
												for="secondaryColor"
												class="block text-sm font-medium text-dark-800 dark:text-gray-100">Secondary
										                                                                           Color</label>
										<div class="flex items-center mt-1">
											<input
													type="color"
													id="secondaryColor"
													wire:model="secondaryColor"
													class="h-8 w-8 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
											<input
													type="text"
													wire:model="secondaryColor"
													class="ml-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
										</div>
										@error('secondaryColor')
										<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
									</div>

									<div class="col-span-1 md:col-span-2 mt-6">
										<x-button type="submit">
											Save Agency Settings
										</x-button>
									</div>
								</div>
							</form>
						</div>
					@endcan
					<!-- Profile Settings Tab -->
					<div class="{{ $activeTab === 'profile' ? 'block' : 'hidden' }}">
						<form wire:submit.prevent="updateUserProfile">
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<!-- Profile Information -->
								<div class="col-span-1 md:col-span-2">
									<h2 class="text-lg mb-4 font-semibold">Profile Information</h2>
								</div>

								<!-- Avatar -->
								<div class="col-span-1 md:col-span-2">
									<div class="flex items-center">
										<div class="mr-4">
											@if ($avatar)
												<img
														src="{{ $avatar->temporaryUrl() }}"
														alt="Avatar Preview"
														class="w-20 h-20 rounded-full object-cover">
											@else
												<img
														src="{{ $user->avatarUrl() }}"
														alt="{{ $user->name }}"
														class="w-20 h-20 rounded-full object-cover">
											@endif
										</div>
										<div>
											<x-upload
													type="file"
													wire:model="avatar"
													id="avatar"
													class="mt-1 block w-full"
													accept="image/*" />
											<p class="text-sm text-gray-500 mt-1">Upload a new profile picture (max
											                                      1MB).</p>
											@error('avatar')
											<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
										</div>
									</div>
								</div>

								<!-- Name -->
								<div>
									<x-input
											label="Name *"
											wire:model="userName"
									/>
								</div>

								<!-- Email -->
								<div>
									<x-input
											label="Email *"
											wire:model="userEmail" />
								</div>

								<!-- Permissions -->
								<div>
									<x-select.styled
											:disabled="authUser()->cannot('update', \tenant())"
											label="Permissions"
											wire:model="permissions"
											:options="collect(\App\Enums\User\UserPermission::cases())->map(fn($permission) => [
																				'label' => $permission->label(),
																				'value' => $permission->value,
																				])->toArray()" />
								</div>

								<!-- Rank or Title -->
								<div>
									<x-input
											label="Rank or Title"
											wire:model="rank_or_title"
									/>

								</div>

								<!-- Badge Number -->
								<div>
									<x-input
											label="Badge Number"
											wire:model="badge_number"
									/>
								</div>

								<!-- License Number -->
								<div>
									<x-input
											label="License Number"
											wire:model="license_number"
									/>
								</div>

								<!-- Department -->
								<div>
									<x-input
											label="Department"
											wire:model="department"
									/>
								</div>

								<!-- Phone -->
								<div>
									<x-input
											label="Phone"
											wire:model="phone"
									/>
								</div>

								<!-- Password Section -->
								<div class="col-span-1 md:col-span-2 mt-6">
									<h2 class="text-lg font-medium mb-4 border-b pb-2">Change Password</h2>
									<p class="text-sm text-gray-500 dark:text-dark-400 mb-4">Leave these fields empty if
									                                                         you don't want to
									                                                         change your password.</p>
								</div>

								<!-- Password -->
								<div>
									<x-password
											label="Password"
											wire:model="password"
									/>
								</div>

								<!-- Password Confirmation -->
								<div>
									<x-password
											label="Confirm Password"
											wire:model="password_confirmation"
									/>
								</div>

								<!-- Submit and Cancel Buttons -->
								<div class="col-span-1 md:col-span-2 mt-6 flex space-x-4">
									<x-button
											wire:click="cancel"
											color="secondary">
										Cancel
									</x-button>
									<x-button
											type="submit"
											color="primary">
										Save Profile
									</x-button>
								</div>
							</div>
						</form>
					</div>

					<!-- Billing Tab -->
					{{--					@can('update', \tenant())--}}
					{{--						<div class="{{ $activeTab === 'billing' ? 'block' : 'hidden' }}">--}}
					{{--							@if ($tenant->subscribed('default'))--}}
					{{--								<livewire:billing.index />--}}
					{{--							@else--}}
					{{--								<livewire:billing.pricing />--}}
					{{--							@endif--}}
					{{--						</div>--}}
					{{--					@endcan--}}
				</div>
			</div>
		</div>
	</div>
</div>