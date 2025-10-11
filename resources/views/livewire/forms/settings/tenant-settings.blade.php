<?php

	use App\Livewire\Forms\UpdateTenantForm;
	use App\Models\Tenant;
	use App\Services\Image\ImageService;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Validation\Rule;
	use Livewire\Volt\Component;
	use Livewire\WithFileUploads;

	new class extends Component {
		use WithFileUploads;

		public UpdateTenantForm $tenantForm;
		public Tenant $tenant;
		public $logo = null;

		public function mount():void
		{
			$this->tenant = Auth::user()->tenant;
			$this->tenantForm->fill($this->tenant);
		}

		public function save():void
		{
			$emailChanged = $this->tenant->agency_email !== $this->tenantForm->agency_email;
			$subdomainChanged = $this->tenant->subdomain !== $this->tenantForm->subdomain;

			$rules = [];
			if ($emailChanged) {
				$rules['tenantForm.agency_email'] = [
					'required', 'email', Rule::unique('tenants', 'agency_email')->ignore($this->tenant->id)
				];
			}
			if ($subdomainChanged) {
				$rules['tenantForm.subdomain'] = [
					'required', Rule::unique('tenants', 'subdomain')->ignore($this->tenant->id)
				];
			}
			if (!empty($rules)) {
				$this->validate($rules);
			}

			$validated = $this->tenantForm->validate();
			$validated = array_filter($validated, static fn($v) => $v !== '' && $v !== null);

			$this->tenant->update($validated);

			if ($this->logo) {
				$imageService = app(ImageService::class);
				foreach ($this->tenant->images as $image) {
					$imageService->deleteImage($image);
				}
				$images = $imageService->uploadImagesForModel([$this->logo], $this->tenant, 'tenants');
				if (count($images) > 0) {
					$imageService->setPrimaryImage($images[0]);
					$this->tenant->logo_path = $images[0]->url();
					$this->tenant->save();
				}
			}

			$this->reset(['logo']);
			session()->flash('message', 'Agency settings updated successfully.');
		}
	};
?>

{{--ToDO: Finish tenant form and User Form--}}


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
			<!-- Agency Name -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="agency_name">Agency Name</label>
				<input
						id="agency_name"
						type="text"
						wire:model="tenantForm.agency_name"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.agency_name')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Subdomain -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="subdomain">Subdomain</label>
				<input
						id="subdomain"
						type="text"
						wire:model="tenantForm.subdomain"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.subdomain')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Agency Type -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="agency_type">Agency Type</label>
				<input
						id="agency_type"
						type="text"
						wire:model="tenantForm.agency_type"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.agency_type')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Agency Email -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="agency_email">Agency Email</label>
				<input
						id="agency_email"
						type="email"
						wire:model="tenantForm.agency_email"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.agency_email')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Agency Phone -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="agency_phone">Agency Phone</label>
				<input
						id="agency_phone"
						type="text"
						wire:model="tenantForm.agency_phone"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.agency_phone')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Agency Website -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="agency_website">Agency Website</label>
				<input
						id="agency_website"
						type="url"
						wire:model="tenantForm.agency_website"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.agency_website')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Billing Email -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="billing_email">Billing Email</label>
				<input
						id="billing_email"
						type="email"
						wire:model="tenantForm.billing_email"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.billing_email')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Billing Phone -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="billing_phone">Billing Phone</label>
				<input
						id="billing_phone"
						type="text"
						wire:model="tenantForm.billing_phone"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.billing_phone')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Tax ID -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="tax_id">Tax ID</label>
				<input
						id="tax_id"
						type="text"
						wire:model="tenantForm.tax_id"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.tax_id')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Address Line 1 -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="address_line1">Address Line 1</label>
				<input
						id="address_line1"
						type="text"
						wire:model="tenantForm.address_line1"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.address_line1')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Address Line 2 -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="address_line2">Address Line 2</label>
				<input
						id="address_line2"
						type="text"
						wire:model="tenantForm.address_line2"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.address_line2')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- City -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="address_city">City</label>
				<input
						id="address_city"
						type="text"
						wire:model="tenantForm.address_city"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.address_city')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- State -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="address_state">State</label>
				<input
						id="address_state"
						type="text"
						wire:model="tenantForm.address_state"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.address_state')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Postal Code -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="address_postal">Postal Code</label>
				<input
						id="address_postal"
						type="text"
						wire:model="tenantForm.address_postal"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.address_postal')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Country -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="address_country">Country</label>
				<input
						id="address_country"
						type="text"
						wire:model="tenantForm.address_country"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.address_country')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Timezone -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="timezone">Timezone</label>
				<input
						id="timezone"
						type="text"
						wire:model="tenantForm.timezone"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.timezone')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Locale -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="locale">Locale</label>
				<input
						id="locale"
						type="text"
						wire:model="tenantForm.locale"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.locale')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Primary Color -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="primary_color">Primary Color</label>
				<input
						id="primary_color"
						type="text"
						wire:model="tenantForm.primary_color"
						placeholder="#000000"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.primary_color')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Secondary Color -->
			<div>
				<label
						class="block text-sm font-medium mb-1"
						for="secondary_color">Secondary Color</label>
				<input
						id="secondary_color"
						type="text"
						wire:model="tenantForm.secondary_color"
						placeholder="#ffffff"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.secondary_color')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Billing Owner ID -->
			<div class="md:col-span-2">
				<label
						class="block text-sm font-medium mb-1"
						for="billing_owner_id">Billing Owner (User ID)</label>
				<input
						id="billing_owner_id"
						type="number"
						wire:model="tenantForm.billing_owner_id"
						class="w-full rounded border-gray-300 dark:bg-dark-800 dark:border-dark-600" />
				@error('tenantForm.billing_owner_id')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<!-- Logo Upload -->
		<div>
			<label
					class="block text-sm font-medium mb-1"
					for="logo">Agency Logo</label>
			<input
					id="logo"
					type="file"
					wire:model="logo"
					class="w-full text-sm" />
			@error('logo')
			<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
			<div
					wire:loading
					wire:target="logo"
					class="text-sm text-gray-500 mt-1">Uploading...
			</div>
		</div>

		<!-- Actions -->
		<div class="flex items-center gap-3">
			<button
					type="submit"
					class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save
			</button>
			<span
					class="text-sm text-gray-500"
					wire:loading.delay.shortest>Saving...</span>
		</div>
	</form>

	@if ($errors->any())
		<div class="mt-4 p-3 rounded bg-red-50 text-red-700">
			<ul class="list-disc list-inside space-y-1">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
</div>
