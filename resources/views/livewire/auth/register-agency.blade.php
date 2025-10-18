<?php

	use App\Enums\Tenant\TenantTypes;
	use App\Livewire\Forms\CreateTenantForm;
	use App\Livewire\Forms\User\UserForm;
	use App\Models\Team;
	use App\Models\Tenant;
	use App\Services\Tenant\TenantCreationService;
	use App\Services\User\CreateUserService;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Hash;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\Title;
	use Livewire\Volt\Component;

 new #[Layout('components.layouts.auth'), Title('Register - PeaceProxy')] class extends Component {
		public CreateTenantForm $tenantForm;
		public UserForm $userForm;

		public function saveTenant():void
		{
			// Validate and create the tenant first
			$validatedTenant = $this->tenantForm->validate();
			$newTenant = app(TenantCreationService::class)
				->createTenant($validatedTenant);

			// Ensure user form is validated using the new tenant context
			$this->userForm->tenantId = $newTenant->id;
			$this->userForm->validate();

			// Build payload from UserForm to ensure defaults, then hash password
			$userData = $this->userForm->payload();
			if (! empty($userData['password'])) {
				$userData['password'] = Hash::make((string) $userData['password']);
			}

			/** @var CreateUserService $userService */
			$userService = app(CreateUserService::class);
			$newUser = $userService->createUserFromTenant($newTenant, $userData);

			$newUser->update([
				'permissions' => 'admin',
				'last_login_ip' => request()->ip(),
				'last_login_at' => now(),
				'primary_team_id' => $this->fetchDefaultTeam($newTenant),
			]);

			$newTenant->update([
				'billing_owner_id' => $newUser->id,
			]);

			// Authenticate the user
			Auth::login($newUser);

			$defaultTeam = Team::where('slug', 'negotiation')->pluck('id');
			$newUser->teams()->attach($defaultTeam, ['is_primary' => true]);

			// Redirect to the tenant dashboard after successful creation
			$tenantSubdomain = $newTenant->subdomain;
			$protocol = request()->secure() ? 'https://' : 'http://';
			$dashboardUrl = "{$protocol}{$tenantSubdomain}.".config('app.domain')."/dashboard";
			$this->redirect($dashboardUrl);
		}

		private function fetchDefaultTeam(Tenant $tenant)
		{
			return $tenant->teams()->where('name', 'Negotiation')->first()->id ?? $tenant->teams()->first()->id;
		}
	}

?>

<div>
	<form
			wire:submit.prevent="saveTenant"
			class="space-y-4">
		<input
				type="hidden"
				name="_token"
				value="{{ csrf_token() }}">
		<!-- Core Identification -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold">Agency Information</h2>
			<p class="mb-2 text-sm text-gray-500">This is the minimum information about your organization needed to
			                                      create your
			                                      poral</p>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-input
						icon="user"
						label="Agency Name *"
						wire:model="tenantForm.agency_name" />
				<x-input
						icon="globe-asia-australia"
						hint="Your agency domain."
						label="Domain *"
						wire:model="tenantForm.subdomain" />
				<x-select.styled
						class="w-full"
						icon="building-office"
						label="Agency Type *"
						value=""
						searchable="true"
						wire:model="tenantForm.agency_type"
						:options="TenantTypes::options()"
				/>
				<x-input
						icon="envelope"
						label="Agency Email"
						type="email"
						wire:model="tenantForm.agency_email" />
				<x-input
						icon="phone"
						x-mask="(999) 999-9999"
						label="Agency Phone"
						wire:model="tenantForm.agency_phone" />
				<x-input
						icon="link"
						label="Agency Website"
						wire:model="tenantForm.agency_website" />
			</div>
		</div>
		<!-- Point of Contact -->
		<div class="mb-6">
			<h2 class="text-lg font-semibold">Point of Contact</h2>
			<p class="mb-2 text-sm text-gray-500">This will be the administrator of your PeaceProxy application. You may
			                                      who this is at a later time.</p>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-input
						icon="user"
						label="Contact Name"
						wire:model="userForm.name" />
				<x-input
						icon="envelope"
						label="Contact Email"
						type="email"
						wire:model="userForm.email" />
				<x-input
						icon="phone"
						label="Contact Phone"
						wire:model="userForm.phone" />
				{{--				<x-select.styled--}}
				{{--						label="Team Affiliation"--}}
				{{--						icon="user"--}}
				{{--						:options="\App\Enums\Team\TeamDiscipline::options()" />--}}
			</div>
		</div>
		<div class="mb-6">
			<h2 class="text-lg font-semibold">Password</h2>
			<p class="mb-2 text-sm text-gray-500">Create the password you will use to login.</p>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<x-password
						wire:model="userForm.password"
						label="Password" />
				<x-password
						label="Confirm Password"
						wire:model="userForm.password_confirmation" />
			</div>
		</div>
		<div class="flex items-center justify-end">
			<x-button type="submit">Submit</x-button>
		</div>
	</form>
</div>
