<?php

	use App\Models\Tenant;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('layouts.app')] class extends Component {
		public $tenant;
		public $agencyName;
		public $agencyEmail;
		public $agencyPhone;
		public $agencyWebsite;
		public $primaryColor;
		public $secondaryColor;

		public function mount()
		{
			$this->tenant = auth()->user()->tenant;
			$this->agencyName = $this->tenant->agency_name;
			$this->agencyEmail = $this->tenant->agency_email;
			$this->agencyPhone = $this->tenant->agency_phone;
			$this->agencyWebsite = $this->tenant->agency_website;
			$this->primaryColor = $this->tenant->primary_color;
			$this->secondaryColor = $this->tenant->secondary_color;
		}

		public function updateSettings()
		{
			$this->validate([
				'agencyName' => 'required|string|max:255',
				'agencyEmail' => 'required|email|max:255',
				'agencyPhone' => 'nullable|string|max:20',
				'agencyWebsite' => 'nullable|url|max:255',
				'primaryColor' => 'nullable|string|max:20',
				'secondaryColor' => 'nullable|string|max:20',
			]);

			$this->tenant->update([
				'agency_name' => $this->agencyName,
				'agency_email' => $this->agencyEmail,
				'agency_phone' => $this->agencyPhone,
				'agency_website' => $this->agencyWebsite,
				'primary_color' => $this->primaryColor,
				'secondary_color' => $this->secondaryColor,
			]);

			session()->flash('message', 'Settings updated successfully.');
		}
	}

?>

<div>
	<div class="">
		<div class="mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-dark-800 overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900 dark:text-gray-100">
					<h1 class="text-2xl font-semibold mb-6">Agency Settings</h1>

					@if (session('message'))
						<div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
							{{ session('message') }}
						</div>
					@endif

					<form wire:submit.prevent="updateSettings">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<!-- Agency Information -->
							<div class="col-span-1 md:col-span-2">
								<h2 class="text-lg font-medium mb-4 border-b pb-2">Agency Information</h2>
							</div>

							<div>
								<label
										for="agencyName"
										class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agency
								                                                                           Name</label>
								<input
										type="text"
										id="agencyName"
										wire:model="agencyName"
										class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
								@error('agencyName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
							</div>

							<div>
								<label
										for="agencyEmail"
										class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agency
								                                                                           Email</label>
								<input
										type="email"
										id="agencyEmail"
										wire:model="agencyEmail"
										class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
								@error('agencyEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
							</div>

							<div>
								<label
										for="agencyPhone"
										class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agency
								                                                                           Phone</label>
								<input
										type="text"
										id="agencyPhone"
										wire:model="agencyPhone"
										class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
								@error('agencyPhone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
							</div>

							<div>
								<label
										for="agencyWebsite"
										class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agency
								                                                                           Website</label>
								<input
										type="url"
										id="agencyWebsite"
										wire:model="agencyWebsite"
										class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
								@error('agencyWebsite')
								<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
							</div>

							<!-- Appearance Settings -->
							<div class="col-span-1 md:col-span-2 mt-6">
								<h2 class="text-lg font-medium mb-4 border-b pb-2">Appearance Settings</h2>
							</div>

							<div>
								<label
										for="primaryColor"
										class="block text-sm font-medium text-gray-700 dark:text-gray-300">Primary
								                                                                           Color</label>
								<div class="flex items-center mt-1">
									<input
											type="color"
											id="primaryColor"
											wire:model="primaryColor"
											class="h-8 w-8 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
									<input
											type="text"
											wire:model="primaryColor"
											class="ml-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
								</div>
								@error('primaryColor')<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
							</div>

							<div>
								<label
										for="secondaryColor"
										class="block text-sm font-medium text-gray-700 dark:text-gray-300">Secondary
								                                                                           Color</label>
								<div class="flex items-center mt-1">
									<input
											type="color"
											id="secondaryColor"
											wire:model="secondaryColor"
											class="h-8 w-8 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
									<input
											type="text"
											wire:model="secondaryColor"
											class="ml-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
								</div>
								@error('secondaryColor')
								<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
							</div>

							<div class="col-span-1 md:col-span-2 mt-6">
								<x-button
										type="submit"
										class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
									Save Settings
								</x-button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>