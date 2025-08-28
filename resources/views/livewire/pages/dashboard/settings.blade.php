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

	new #[Layout('layouts.app')] class extends Component {
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
 	public $activeTab = 'agency'; // 'agency' or 'profile'

		public function mount()
		{
			// Check if tab parameter is present in the request
			if (request()->has('tab') && in_array(request()->tab, ['agency', 'profile'])) {
				$this->activeTab = request()->tab;
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

		public function setActiveTab($tab)
		{
			$this->activeTab = $tab;
		}

 	public function updateAgencySettings()
 	{
 		$this->validate([
 			'agencyName' => 'required|string|max:255',
 			'agencyEmail' => 'required|email|max:255',
 			'agencyPhone' => 'nullable|string|max:20',
 			'agencyWebsite' => 'nullable|url|max:255',
 			'agencyType' => 'required|string|in:' . implode(',', array_column(TenantTypes::toArray(), 'value')),
 			'primaryColor' => 'nullable|string|max:20',
 			'secondaryColor' => 'nullable|string|max:20',
 			'logo' => 'nullable|image|max:1024', // 1MB max
 		]);

 		$this->tenant->update([
 			'agency_name' => $this->agencyName,
 			'agency_email' => $this->agencyEmail,
 			'agency_phone' => $this->agencyPhone,
 			'agency_website' => $this->agencyWebsite,
 			'agency_type' => $this->agencyType,
 			'primary_color' => $this->primaryColor,
 			'secondary_color' => $this->secondaryColor,
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

 	public function updateUserProfile()
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

		public function cancel()
		{
			// Redirect to the dashboard
			return redirect()->route('dashboard', ['tenantSubdomain' => tenant()->subdomain]);
		}
	}

?>

<div>
	<div class="">
		<div class="mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-dark-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-300 dark:border-dark-600">
				<div class="p-6 text-dark-800 dark:text-white">
					<h4 class="text-lg p-2 bg-gray-200/50 dark:text-white dark:bg-dark-800/50 rounded-t-lg mb-4">Settings</h4>

					@if (session('message'))
						<div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
							{{ session('message') }}
						</div>
					@endif

					<!-- Tabs -->
					<div class="mb-6 border-b border-gray-200">
						<ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
							<li class="mr-2">
								<button 
									wire:click="setActiveTab('agency')" 
									class="inline-block p-4 {{ $activeTab === 'agency' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
								>
									Agency Settings
								</button>
							</li>
							<li class="mr-2">
								<button 
									wire:click="setActiveTab('profile')" 
									class="inline-block p-4 {{ $activeTab === 'profile' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
								>
									Profile Settings
								</button>
							</li>
						</ul>
					</div>

					<!-- Agency Settings Tab -->
					<div class="{{ $activeTab === 'agency' ? 'block' : 'hidden' }}">
						<form wire:submit.prevent="updateAgencySettings">
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<!-- Agency Information -->
								<div class="col-span-1 md:col-span-2">
									<h2 class="text-lg font-medium mb-4 border-b pb-2">Agency Information</h2>
								</div>

								<div>
									<label
											for="agencyName"
											class="block text-sm font-medium text-dark-800 dark:text-gray-100">Agency Name</label>
									<input
											type="text"
											id="agencyName"
											wire:model="agencyName"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('agencyName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<div>
									<label
											for="agencyEmail"
											class="block text-sm font-medium text-dark-800 dark:text-gray-100">Agency Email</label>
									<input
											type="email"
											id="agencyEmail"
											wire:model="agencyEmail"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('agencyEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<div>
									<label
											for="agencyPhone"
											class="block text-sm font-medium text-dark-800 dark:text-gray-100">Agency Phone</label>
									<input
											type="text"
											id="agencyPhone"
											wire:model="agencyPhone"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('agencyPhone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<div>
									<label
											for="agencyWebsite"
											class="block text-sm font-medium text-dark-800 dark:text-gray-100">Agency Website</label>
									<input
											type="url"
											id="agencyWebsite"
											wire:model="agencyWebsite"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('agencyWebsite')
									<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<div>
									<label
											for="agencyType"
											class="block text-sm font-medium text-dark-800 dark:text-gray-100">Agency Type</label>
									<select
											id="agencyType"
											wire:model="agencyType"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
										<option value="">Select Agency Type</option>
										@foreach(TenantTypes::toArray() as $type)
											<option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
										@endforeach
									</select>
									@error('agencyType')
									<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
											<p class="text-sm text-gray-500 mt-1">Upload a new agency logo (max 1MB).</p>
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
											class="block text-sm font-medium text-dark-800 dark:text-gray-100">Primary Color</label>
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
									@error('primaryColor')<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<div>
									<label
											for="secondaryColor"
											class="block text-sm font-medium text-dark-800 dark:text-gray-100">Secondary Color</label>
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

					<!-- Profile Settings Tab -->
					<div class="{{ $activeTab === 'profile' ? 'block' : 'hidden' }}">
						<form wire:submit.prevent="updateUserProfile">
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<!-- Profile Information -->
								<div class="col-span-1 md:col-span-2">
									<h2 class="text-lg font-medium mb-4 border-b pb-2">Profile Information</h2>
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
											<p class="text-sm text-gray-500 mt-1">Upload a new profile picture (max 1MB).</p>
											@error('avatar')
											<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
										</div>
									</div>
								</div>

								<!-- Name -->
								<div>
									<label for="name" class="block text-sm font-medium text-dark-800 dark:text-gray-100">Name</label>
									<input
											type="text"
											id="name"
											wire:model="userName"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('userName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<!-- Email -->
								<div>
									<label for="email" class="block text-sm font-medium text-dark-800 dark:text-gray-100">Email</label>
									<input
											type="email"
											id="email"
											wire:model="userEmail"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('userEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<!-- Permissions -->
								<div>
									<label for="permissions" class="block text-sm font-medium text-dark-800 dark:text-gray-100">Permissions</label>
									<input
											type="text"
											id="permissions"
											wire:model="permissions"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('permissions') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<!-- Rank or Title -->
								<div>
									<label for="rank_or_title" class="block text-sm font-medium text-dark-800 dark:text-gray-100">Rank or Title</label>
									<input
											type="text"
											id="rank_or_title"
											wire:model="rank_or_title"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('rank_or_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<!-- Badge Number -->
								<div>
									<label for="badge_number" class="block text-sm font-medium text-dark-800 dark:text-gray-100">Badge Number</label>
									<input
											type="text"
											id="badge_number"
											wire:model="badge_number"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('badge_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<!-- License Number -->
								<div>
									<label for="license_number" class="block text-sm font-medium text-dark-800 dark:text-gray-100">License Number</label>
									<input
											type="text"
											id="license_number"
											wire:model="license_number"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('license_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<!-- Department -->
								<div>
									<label for="department" class="block text-sm font-medium text-dark-800 dark:text-gray-100">Department</label>
									<input
											type="text"
											id="department"
											wire:model="department"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('department') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<!-- Phone -->
								<div>
									<label for="phone" class="block text-sm font-medium text-dark-800 dark:text-gray-100">Phone</label>
									<input
											type="text"
											id="phone"
											wire:model="phone"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<!-- Password Section -->
								<div class="col-span-1 md:col-span-2 mt-6">
									<h2 class="text-lg font-medium mb-4 border-b pb-2">Change Password</h2>
									<p class="text-sm text-gray-500 mb-4">Leave these fields empty if you don't want to change your password.</p>
								</div>

								<!-- Password -->
								<div>
									<label for="password" class="block text-sm font-medium text-dark-800 dark:text-gray-100">New Password</label>
									<input
											type="password"
											id="password"
											wire:model="password"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
									@error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
								</div>

								<!-- Password Confirmation -->
								<div>
									<label for="password_confirmation" class="block text-sm font-medium text-dark-800 dark:text-gray-100">Confirm Password</label>
									<input
											type="password"
											id="password_confirmation"
											wire:model="password_confirmation"
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
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
				</div>
			</div>
		</div>
	</div>
</div>