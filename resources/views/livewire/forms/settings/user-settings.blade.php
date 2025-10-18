<?php

	use App\Livewire\Forms\UpdateUserForm;
	use App\Livewire\Forms\User\UserForm;
	use App\Models\User;
	use App\Services\Image\AvatarService;
	use App\Services\Image\ImageService;
	use App\Services\Team\TeamFetchingService;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Validation\Rule;
	use Illuminate\Validation\Rules\Password as PasswordRule;
	use Livewire\Attributes\Computed;
	use Livewire\Volt\Component;
	use Livewire\WithFileUploads;
	use TallStackUi\Traits\Interactions;

	new class extends Component {
		use WithFileUploads;

		public UserForm $userForm;
		public User $user;
		public $avatar = null;
		public ?string $image = null;
		public int $avatarVersion = 0;

		use Interactions;

		public function mount():void
		{
			$this->user = Auth::user();
			$this->userForm->setUser($this->user, $this->user->tenant_id);
			$this->image = $this->user->avatarUrl();
		}

		public function save(AvatarService $avatarService):void
		{
			try {
				$this->userForm->validate();

				if ($this->avatar) {
					$newAvatar = $avatarService->set($this->user, $this->avatar);
					$this->image = $newAvatar->url;
				} else {
					$this->image = $this->user->avatarUrl();
				}

				$this->userForm->update();

				$this->reset(['avatar']);

				$this->toast()
					->success('Your profile was updated successfully!')
					->send();

			} catch (Throwable $e) {
				logger()->error('Failed to save avatar', [
					'user_id' => $this->user->id,
					'error' => $e->getMessage(),
				]);

				$this->toast()
					->danger('There was a problem when updating your profile. Please Try again.')
					->send();
			}
		}

		public function clearAvatar(Image $image):void
		{
			try {
				app(ImageService::class)->deleteImage($this->user->avatar);

				$this->user->update(['avatar_path' => '']);

				$this->user->refresh();
				$this->image = $this->user->avatarUrl();
				$this->avatarVersion++;

				$this->toast()
					->success('Avatar deleted successfully!')
					->send();

			} catch (Throwable $e) {
				// Log for debugging (or use Sentry, Bugsnag, etc.)
				logger()->error('Failed to delete avatar', [
					'user_id' => $this->user->id,
					'error' => $e->getMessage(),
				]);

				// Gracefully notify the user
				$this->toast()
					->error('There was a problem deleting your avatar. Please try again.')
					->send();
			}
		}

		#[Computed]
		public function getTeams()
		{
			return app(TeamFetchingService::class)->fetchTeamOptions();
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
		<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
			<div class="col-span-full">
				<x-ui.text-center-divider>Personal Information</x-ui.text-center-divider>
			</div>
			<div class="flex gap-4">
				<div
						class="relative group"
						wire:key="avatar-version-{{ $avatarVersion }}">
					<img
							alt="User Avatar"
							src="{{ $avatar ? $avatar->temporaryUrl() : $image }}"
							class="rounded-sm h-20 w-20 object-cover" />
					@if ($user->avatar_path)
						<div class="absolute bottom-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
							<x-button.circle
									wire:click="clearAvatar"
									color="red"
									xs
									icon="x-mark" />
						</div>
					@endif
				</div>
				<div class="flex-1">
					<x-upload
							class="w-full"
							label="Avatar"
							wire:model="avatar" />
					<div
							wire:loading
							wire:target="avatar"
							class="text-sm text-gray-500 mt-1">Uploading...
					</div>
				</div>
			</div>
			<div>
				<x-input
						label="Name *"
						wire:model="userForm.name" />
			</div>

			<!-- Email -->
			<div>
				<x-input
						label="Email *"
						wire:model="userForm.email" />
			</div>

			<!-- Phone -->
			<div>
				<x-input
						x-mask="(999)-999-9999"
						label="Phone"
						wire:model="userForm.phone" />
			</div>
			<div>
				<x-select.styled
						:options="$this->getTeams()"
						label="Primary Team Discipline"
						wire:model="userForm.primary_team_id" />
			</div>

			<div>
				<x-input
						label="Alternate Email"
						wire:model="userForm.alternate_email" />
			</div>

			<div class="col-span-full">
				<x-ui.text-center-divider>Agency Information</x-ui.text-center-divider>
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

			<div>
				<x-input
						label="License Number"
						wire:model="userForm.license_number" />
			</div>

			<div class="col-span-full">
				<x-ui.text-center-divider>local Information</x-ui.text-center-divider>
			</div>
			<!-- Locale -->
			<div>
				<x-input
						disabled
						label="Locale *"
						wire:model="userForm.locale" />
			</div>

			<!-- Timezone -->
			<div>
				<x-input
						disabled
						label="Timezone *"
						wire:model="userForm.timezone" />
			</div>
		</div>

		<div class="col-span-full">
			<x-ui.text-center-divider>Passwords</x-ui.text-center-divider>
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

		<!-- Actions -->
		<div class="flex items-center gap-3">
			<x-button
					text="Save"
					type="submit" />
		</div>
	</form>
</div>
