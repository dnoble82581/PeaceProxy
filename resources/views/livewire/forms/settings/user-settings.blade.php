<?php

	use App\Livewire\Forms\UpdateUserForm;
	use App\Models\User;
	use App\Services\Image\ImageService;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Validation\Rule;
	use Illuminate\Validation\Rules\Password as PasswordRule;
	use Livewire\Volt\Component;
	use Livewire\WithFileUploads;

	new class extends Component {
		use WithFileUploads;

		public UpdateUserForm $userForm;
		public User $user;
		public $avatar = null;

		public function mount():void
		{
			$this->user = Auth::user();
			$this->userForm->fill($this->user);
		}

		public function save():void
		{
			$emailChanged = $this->user->email !== $this->userForm->email;
			$passwordProvided = filled($this->userForm->password);

			$rules = [];
			if ($emailChanged) {
				$rules['userForm.email'] = [
					'required', 'email', 'max:254', Rule::unique('users', 'email')->ignore($this->user->id)
				];
			}
			if ($passwordProvided) {
				$rules['userForm.password'] = ['nullable', 'string', PasswordRule::defaults(), 'confirmed'];
				$rules['userForm.password_confirmation'] = ['nullable', 'string'];
			}
			if (!empty($rules)) {
				$this->validate($rules);
			}

			$validated = $this->userForm->validate();

			if (empty($this->userForm->password)) {
				unset($validated['password']);
			} else {
				$validated['password'] = Hash::make($this->userForm->password);
			}

			$validated = array_filter($validated, static fn($v) => $v !== '' && $v !== null);

			$this->user->update($validated);

			if ($this->avatar) {
				$imageService = app(ImageService::class);
				foreach ($this->user->images as $image) {
					$imageService->deleteImage($image);
				}
				$images = $imageService->uploadImagesForModel([$this->avatar], $this->user, 'users', 's3_public');
				if (count($images) > 0) {
					$imageService->setPrimaryImage($images[0]);
				}
			}

			$this->reset(['avatar']);
			session()->flash('message', 'Profile updated successfully.');
		}
	};
?>

<div class="space-y-6">
	@if (session('message'))
		<div
				x-data="{ show: true }"
				x-init="setTimeout(() => { show = false }, 3000)"
				x-show="show"
				x-transition:leave="transition ease-in duration-300"
				x-transition:leave-start="opacity-100"
				x-transition:leave-end="opacity-0"
				class="p-3 rounded bg-green-100 text-green-800"
		>
			{{ session('message') }}
		</div>
	@endif

	<form
			wire:submit.prevent="save"
			class="space-y-6">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<!-- Name -->
			<div>
				<x-input
						label="Name"
						wire:model="userForm.name" />
			</div>

			<!-- Email -->
			<div>
				<x-input
						label="Email"
						wire:model="userForm.email" />
			</div>

			<!-- Phone -->
			<div>
				<x-input
						label="Phone"
						wire:model="userForm.phone" />
			</div>

			<!-- Department -->
			<div>
				<x-input
						label="Department"
						wire:model="userForm.department" />
			</div>

			<!-- Rank / Title -->
			<div>
				<x-input
						label="Rank / Title"
						wire:model="userForm.rank_or_title" />
			</div>

			<!-- Badge Number -->
			<div>
				<x-input
						label="Badge Number"
						wire:model="userForm.badge_number" />
			</div>

			<!-- Locale -->
			<div>
				<x-input
						label="Locale"
						wire:model="userForm.locale" />
			</div>

			<!-- Timezone -->
			<div>
				<x-input
						label="Timezone"
						wire:model="userForm.timezone" />
			</div>
		</div>

		<!-- Passwords -->
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div>
				<x-password label="Current Password" />
			</div>
			<div>
				<x-password label="Password Confirmation" />
			</div>
		</div>

		<!-- Avatar Upload -->
		<div>
			<x-upload
					label="Avatar"
					wire:model="avatar" />
			<div
					wire:loading
					wire:target="avatar"
					class="text-sm text-gray-500 mt-1">Uploading...
			</div>
		</div>

		<!-- Actions -->
		<div class="flex items-center gap-3">
			<x-button
					text="Save"
					type="submit" />
		</div>
	</form>

	{{--	@if ($errors->any())--}}
	{{--		<div class="mt-4 p-3 rounded bg-red-50 text-red-700">--}}
	{{--			<ul class="list-disc list-inside space-y-1">--}}
	{{--				@foreach ($errors->all() as $error)--}}
	{{--					<li>{{ $error }}</li>--}}
	{{--				@endforeach--}}
	{{--			</ul>--}}
	{{--		</div>--}}
	{{--	@endif--}}
</div>
