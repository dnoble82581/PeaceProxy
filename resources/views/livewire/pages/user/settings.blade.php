<?php

	use App\Models\User;
	use App\Services\Image\ImageService;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Validation\Rules;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Livewire\WithFileUploads;

	new #[Layout('layouts.negotiation')] class extends Component {
		use WithFileUploads;

		public User $user;
		public ?string $password = null;
		public ?string $password_confirmation = null;
		public $avatar = null;

		public function mount()
		{
			$this->user = Auth::user();
		}

		public function rules()
		{
			return [
				'user.name' => ['required', 'string', 'max:255'],
				'user.email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
				'password' => ['nullable', 'string', 'confirmed', Rules\Password::defaults()],
				'avatar' => ['nullable', 'image', 'max:1024'], // 1MB max
			];
		}

		public function save()
		{
			$this->validate();

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

			$this->redirect(request()->header('Referer'));

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
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-dark-800 overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900 dark:text-gray-100">
					<h1 class="text-2xl font-semibold mb-6">User Settings</h1>

					@if (session('message'))
						<div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
							{{ session('message') }}
						</div>
					@endif

					<form wire:submit.prevent="save">
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
										wire:model="user.name"
										label="Name"
										id="name"
										class="block mt-1 w-full"
										type="text" />
								@error('user.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
							</div>

							<!-- Email -->
							<div>
								<x-input
										wire:model="user.email"
										label="Email"
										id="email"
										class="block mt-1 w-full"
										type="email" />
								@error('user.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
							</div>

							<!-- Password Section -->
							<div class="col-span-1 md:col-span-2 mt-6">
								<h2 class="text-lg font-medium mb-4 border-b pb-2">Change Password</h2>
								<p class="text-sm text-gray-500 mb-4">Leave these fields empty if you don't want to
								                                      change your password.</p>
							</div>

							<!-- Password -->
							<div>
								<x-input
										wire:model="password"
										label="New Password"
										id="password"
										class="block mt-1 w-full"
										type="password" />
								@error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
							</div>

							<!-- Password Confirmation -->
							<div>
								<x-input
										wire:model="password_confirmation"
										label="Confirm Password"
										id="password_confirmation"
										class="block mt-1 w-full"
										type="password" />
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
									Save Changes
								</x-button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>