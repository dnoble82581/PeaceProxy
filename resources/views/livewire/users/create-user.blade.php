<?php

	use App\Livewire\Forms\User\UserForm;
	use App\Models\Team;
	use App\Models\User;
	use Illuminate\Support\Facades\Hash;
	use Livewire\Attributes\Validate;
	use Livewire\Volt\Component;

	new class extends Component {
		public UserForm $userForm;
		public array $teamOptions;

		public function mount():void
		{
			$this->teamOptions = $this->fetchTeamOptions();
		}

		public function save()
		{
			$this->userForm->create();
			$this->userForm->reset();
			$this->dispatch('close-modals');
		}

		public function fetchTeamOptions()
		{
			$teams = Team::all(['id', 'name']);

			return $teams->map(function ($team) {
				return [
					'label' => $team->name,
					'value' => $team->id,
				];
			})->toArray();
		}
	}

?>

<x-card>
	<form
			wire:submit.prevent="save"
			id="user-create"
			class="space-y-4">
		<div>
			<x-input
					label="Name *"
					wire:model="userForm.name"
					required />
		</div>

		<div>
			<x-input
					label="{{ __('Email') }} *"
					wire:model="userForm.email"
					required />
		</div>
		<div>
			<x-select.styled
					label="Primary Team *"
					wire:model="userForm.primary_team_id"
					searchable
					:options="$teamOptions"
					required />
		</div>

		<div>
			<x-password
					label="{{ __('Password') }} *"
					wire:model="userForm.password"
					generator
					x-on:generate="$wire.set('userForm.password_confirmation', $event.detail.password)"
					required />
		</div>

		<div>
			<x-password
					:label="__('Confirm Password')"
					wire:model="userForm.password_confirmation"
					required />
		</div>
		<div>
			<x-button
					type="submit"
					text="Create" />
		</div>
	</form>
</x-card>
