<?php

	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('layouts.auth')] class extends Component {
		public string $email = '';
		public string $password = '';
		public bool $rememberMe = false;

		public function login()
		{

			$this->validate([
				'email' => 'required|email',
				'password' => 'required',
				'rememberMe' => 'bool|nullable'
			]);

			if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->rememberMe)) {
				session()->regenerate();
				session()->forget('url.intended');

				$user = Auth::user();

				// Safeguard against missing tenant
				if (!$user->tenant || !$user->tenant->subdomain) {
					Auth::logout();
					return redirect()->route('login')->withErrors([
						'email' => 'Tenant or subdomain information is missing. Please contact support.',
					]);
				}

				// Redirect to tenant-specific dashboard
				$subdomain = $user->tenant->subdomain;

				return redirect()->to(route('dashboard', ['tenantSubdomain' => $subdomain,]));
			}
			$this->addError('email', 'Invalid login credentials.');
		}
	}

?>

<div>
	<form
			wire:submit.prevent="login"
			class="space-y-4">
		<input
				type="hidden"
				name="_token"
				value="{{ csrf_token() }}">
		<x-input
				wire:model.lazy="email"
				placeholder="Email"
				label="Email" />

		<x-password
				type="password"
				wire:model.lazy="password"
				placeholder="Password"
				label="Password" />

		<x-checkbox
				wire:model="rememberMe"
				label="Remember me" />

		<div class="flex justify-end">
			<x-button
					type="submit">Login
			</x-button>
		</div>
	</form>
</div>
