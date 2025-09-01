<?php

	use App\Livewire\Forms\CreateTenantForm;
	use App\Livewire\Forms\CreateUserForm;
	use App\Services\Tenant\TenantCreationService;
	use App\Services\User\CreateUserService;
	use Illuminate\Support\Facades\Auth;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('layouts.auth')] class extends Component {
		public CreateTenantForm $tenantForm;
		public CreateUserForm $userForm;

		public function saveTenant():void
		{
			$validatedTenant = $this->tenantForm->validate();
			$validatedUser = $this->userForm->validate();
			$newTenant = app(TenantCreationService::class)
				->createTenant($validatedTenant);
			$newUser = app(CreateUserService::class)
				->createUserFromTenant($newTenant, $validatedUser);

			$newUser->update([
				'permissions' => 'admin',
				'last_login_ip' => request()->ip(),
				'last_login_at' => now()
			]);

			$newTenant->update([
				'billing_owner_id' => $newUser->id
			]);

			// Authenticate the user
			Auth::login($newUser);

			// Redirect to the tenant dashboard after successful creation
			$tenantSubdomain = $newTenant->subdomain;
			$protocol = request()->secure()? 'https://' : 'http://';
			$dashboardUrl = "{$protocol}{$tenantSubdomain}.".config('app.domain')."/dashboard";
			$this->redirect($dashboardUrl);
		}
	}

?>

<div>
	@if (session()->has('csrf_debug'))
		<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
			<h3 class="font-bold">CSRF Debug Information</h3>
			<ul class="list-disc pl-5">
				<li>Session Token: {{ session('csrf_debug.session_token') }}</li>
				<li>Header Token: {{ session('csrf_debug.header_token') }}</li>
				<li>XSRF Token: {{ session('csrf_debug.xsrf_token') }}</li>
				<li>Input Token: {{ session('csrf_debug.input_token') }}</li>
				<li>Session ID: {{ session('csrf_debug.session_id') }}</li>
				<li>Session Domain: {{ session('csrf_debug.session_domain') ?: 'Not configured' }}</li>
				<li>App Domain: {{ session('csrf_debug.app_domain') }}</li>
			</ul>
		</div>
	@endif

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
						wire:model="tenantForm.agency_type"
						:options="collect(\App\Enums\Tenant\TenantTypes::cases())->map(fn($type) => [
																				'label' => $type->label(),
																				'value' => $type->value,
																				])->toArray()"
				/>
				<x-input
						icon="envelope"
						label="Agency Email"
						type="email"
						wire:model="tenantForm.agency_email" />
				<x-input
						icon="phone"
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
						label="Confirm Password" />
			</div>
		</div>
		<div class="flex items-center justify-end">
			<x-button type="submit">Submit</x-button>
		</div>
	</form>
</div>
