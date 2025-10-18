<?php

	use App\Livewire\Forms\UpdateTenantForm;
	use App\Models\Tenant;
	use App\Services\Image\ImageService;
	use App\Services\Image\LogoService;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Validation\Rule;
	use Livewire\Volt\Component;
	use Livewire\WithFileUploads;
	use TallStackUi\Traits\Interactions;

	new class extends Component {
		use WithFileUploads;
		use Interactions;

		public UpdateTenantForm $tenantForm;
		public Tenant $tenant;
		public $logo = null;
		public int $logoVersion = 0;
		public ?string $image = null;
		public ?string $primaryColor = null;
		public ?string $secondaryColor = null;

		public function mount():void
		{
			$this->tenant = Auth::user()->tenant;
			$this->tenantForm->setTenant($this->tenant);
			$this->image = $this->tenant->logoUrl();
			$this->primaryColor = $this->tenant->primary_color;
			$this->secondaryColor = $this->tenant->secondary_color;
		}

		public function save(LogoService $logoService):void
		{
			try {
				$this->tenantForm->validate();

				if ($this->logo) {
					$newLogo = $logoService->set($this->tenant, $this->logo);
					$this->image = $newLogo->url;
				} else {
					$this->image = $this->tenant->logoUrl();
				}

				$this->tenantForm->update();

				$this->reset(['logo']);

				$this->toast()
					->success('Your tenant profile was updated successfully!')
					->send();

			} catch (Throwable $e) {
				logger()->error('Failed to save avatar', [
					'user_id' => $this->tenant->id,
					'error' => $e->getMessage(),
				]);
				$this->toast()
					->danger('There was a problem when updating your profile. Please Try again.')
					->send();
			}
		}

		public function clearLogo()
		{
			try {
				app(ImageService::class)->deleteImage($this->tenant->logo);

				$this->tenant->update(['logo_path' => '']);

				$this->tenant->refresh();
				$this->image = $this->tenant->logoUrl();
				$this->logoVersion++;

				$this->toast()
					->success('Avatar deleted successfully!')
					->send();

			} catch (Throwable $e) {
				// Log for debugging (or use Sentry, Bugsnag, etc.)
				logger()->error('Failed to delete avatar', [
					'user_id' => $this->tenant->id,
					'error' => $e->getMessage(),
				]);

				// Gracefully notify the user
				$this->toast()
					->error('There was a problem deleting your avatar. Please try again.')
					->send();
			}
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
				<x-ui.text-center-divider>Branding</x-ui.text-center-divider>
			</div>
			<div class="flex gap-4 col-span-full">
				<div class="relative group">
					<img
							alt="Agency Logo"
							src="{{ $logo ? $logo->temporaryUrl() : $image }}"
							class="rounded-sm h-20 w-20 object-cover" />
					@if ($tenant->logo_path)
						<div class="absolute bottom-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
							<x-button.circle
									wire:click="clearLogo"
									color="red"
									xs
									icon="x-mark" />
						</div>
					@endif
				</div>
				<div class="">
					<x-upload
							class="w-full"
							label="Agency Logo"
							wire:model="logo" />
					<div
							wire:loading
							wire:target="logo"
							class="text-sm text-gray-500 mt-1">Uploading...
					</div>
				</div>
			</div>

			<div class="col-span-full">
				<x-ui.text-center-divider>Agency Information</x-ui.text-center-divider>
			</div>
			<div>
				<x-input
						label="Agency Name *"
						wire:model="tenantForm.agency_name" />
			</div>
			<div>
				<x-input
						label="Subdomain *"
						icon="globe-asia-australia"
						wire:model="tenantForm.subdomain" />
			</div>
			<div>
				<x-input
						label="Agency Type *"
						wire:model="tenantForm.agency_type" />
			</div>

			<div class="col-span-full">
				<x-ui.text-center-divider>Contact & Web</x-ui.text-center-divider>
			</div>
			<div>
				<x-input
						type="email"
						label="Agency Email"
						icon="envelope"
						wire:model="tenantForm.agency_email" />
			</div>
			<div>
				<x-input
						label="Agency Phone"
						icon="phone"
						wire:model="tenantForm.agency_phone" />
			</div>
			<div>
				<x-input
						label="Agency Website"
						icon="link"
						wire:model="tenantForm.agency_website" />
			</div>

			<div class="col-span-full">
				<x-ui.text-center-divider>Billing</x-ui.text-center-divider>
			</div>
			<div>
				<x-input
						type="email"
						label="Billing Email"
						wire:model="tenantForm.billing_email" />
			</div>
			<div>
				<x-input
						label="Billing Phone"
						wire:model="tenantForm.billing_phone" />
			</div>
			<div>
				<x-input
						label="Tax ID"
						wire:model="tenantForm.tax_id" />
			</div>
			{{--			<div class="col-span-full">--}}
			{{--				<x-input--}}
			{{--						type="number"--}}
			{{--						label="Billing Owner (User ID)"--}}
			{{--						wire:model="tenantForm.billing_owner_id" />--}}
			{{--			</div>--}}

			{{--			<div class="col-span-full">--}}
			{{--				<x-ui.text-center-divider>Address</x-ui.text-center-divider>--}}
			{{--			</div>--}}
			<div>
				<x-input
						label="Address Line 1"
						wire:model="tenantForm.address_line1" />
			</div>
			<div>
				<x-input
						label="Address Line 2"
						wire:model="tenantForm.address_line2" />
			</div>
			<div>
				<x-input
						label="City"
						wire:model="tenantForm.address_city" />
			</div>
			<div>
				<x-input
						label="State"
						wire:model="tenantForm.address_state" />
			</div>
			<div>
				<x-input
						label="Postal Code"
						wire:model="tenantForm.address_postal" />
			</div>
			<div>
				<x-input
						label="Country"
						wire:model="tenantForm.address_country" />
			</div>

			<div class="col-span-full">
				<x-ui.text-center-divider>Localization & Theme</x-ui.text-center-divider>
			</div>
			<div>
				<x-input
						label="Timezone"
						wire:model="tenantForm.timezone" />
			</div>
			<div>
				<x-input
						label="Locale"
						wire:model="tenantForm.locale" />
			</div>
			<div>
				<x-color
						picker
						label="Primary Color"
						placeholder="#000000"
						wire:model.live="primaryColor" />
			</div>
			<div>
				<x-color
						picker
						label="Secondary Color"
						placeholder="#ffffff"
						wire:model.live="secondaryColor" />
			</div>
		</div>

		<div class="flex items-center gap-3">
			<x-button
					text="Save"
					type="submit" />
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
