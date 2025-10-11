<?php

	use App\Enums\Tenant\TenantTypes;
	use App\Models\Tenant;
	use App\Models\User;
	use App\Services\Image\ImageService;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Validation\Rule;
	use Illuminate\Validation\Rules;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Livewire\WithFileUploads;

	new #[Layout('layouts.app'), \Livewire\Attributes\Title('Settings - Peace Proxy')] class extends Component {


		public \App\Livewire\Forms\UpdateTenantForm $tenantForm;
		public \App\Livewire\Forms\UpdateUserForm $userForm;

		public Tenant $tenant;
		public User $user;


		// UI state
		public $activeTab = 'profile';

		public function mount()
		{
			$this->tenant = \tenant();
		}


	}

?>

<div>
	<x-tab
			selected="Profile"
			scroll-on-mobile>
		<x-tab.items tab="Profile">
			<livewire:forms.settings.user-settings />
		</x-tab.items>
		@can('update', $this->tenant)
			<x-tab.items tab="Agency">
				<livewire:forms.settings.tenant-settings />
			</x-tab.items>
		@endcan
	</x-tab>
</div>