<?php

	use App\Models\User;
	use Illuminate\Validation\Rule;
	use Livewire\Attributes\On;
	use TallStackUi\Traits\Interactions;

	new class extends \Livewire\Volt\Component {
		use Interactions;

		public \App\Livewire\Forms\User\UserForm $form;
		public bool $showUpdateModal = false;
		public User $user;


		#[On('load::user')]
		public function loadForm(User $user):void
		{
			$this->form->setUser($user, $user->tenant_id);
			$this->showUpdateModal = true;
		}

		public function save():void
		{
			$this->form->update();
			$this->showUpdateModal = false;
			$this->dispatch('updated');
		}
	}

?>
{{--TODO: update create user to use the new form.--}}
<div>
	<x-modal
			wire="showUpdateModal">
		<x-slot:title>
			<h3 class="text-lg flex-1 font-medium">
				Updating User: <span class="text-primary-500">{{ $user->name ?? 'Unknown' }}</span>
			</h3>
		</x-slot:title>
		<form
				id="user-update-{{ $user?->id }}"
				wire:submit="save"
				class="space-y-4">
			<div>
				<x-input
						label="{{ __('Name') }} *"
						wire:model="form.name"
						required />
			</div>

			<div>
				<x-input
						label="{{ __('Email') }} *"
						wire:model="form.email"
						required />
			</div>

			<div>
				<x-select.styled
						label="Team Discipline"
						wire:model="form.primary_team_id"
						:options="App\Models\Team::all()->map(fn($team) => ['label' => $team->name, 'value' => $team->id])->toArray()" />
			</div>

			<div>
				<x-select.styled
						:disabled="!authUser() || !tenant() || authUser()->cannot('update', tenant()) || authUser()->id === $user?->id"
						label="Permissions"
						wire:model="form.permissions"
						:options="collect(App\Enums\User\UserPermission::cases())->map(fn($permission) => [
																'label' => $permission->label(),
																'value' => $permission->value,
																])->toArray()" />
			</div>

			<div>
				<x-password
						:label="__('Password')"
						hint="The password will only be updated if you set the value of this field"
						wire:model="form.password"
						rules
						generator
						x-on:generate="$wire.set('password_confirmation', $event.detail.password)" />
			</div>

			<div>
				<x-password
						:label="__('Password')"
						wire:model="form.password_confirmation"
						rules />
			</div>
		</form>
		<x-slot:footer>
			<x-button
					type="submit"
					form="user-update-{{ $user?->id }}"
					loading="save">
				@lang('Save')
			</x-button>
		</x-slot:footer>
	</x-modal>
</div>
