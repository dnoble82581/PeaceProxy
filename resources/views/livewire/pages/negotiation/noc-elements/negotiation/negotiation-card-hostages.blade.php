<?php

	use App\Models\Negotiation;
	use App\Services\Negotiation\NegotiationFetchingService;
	use Livewire\Volt\Component;

	new class extends Component {
		public Negotiation $negotiation;

		public function mount($negotiationId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)->getNegotiationById($negotiationId);
		}
	}

?>

<div class="dark:bg-dark-800 p-4 overflow-visible">
	<div class="text-right px-4 pt-1">
		<x-button
				wire:navigate.hover
				href="{{ route('hostage.create', ['negotiation' => $negotiation->id, 'tenantSubdomain' => tenant()->subdomain]) }}"
				color=""
				flat
				sm
				icon="plus"
		/>
	</div>
	<ul
			role="list"
			class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 overflow-visible">
		@forelse($this->negotiation->hostages as $hostage)
			<li class="col-span-1 flex rounded-md shadow-xs dark:shadow-none dark:bg-dark-600 overflow-visible">
				<div class="flex w-16 shrink-0 items-center justify-center rounded-l-md text-sm font-medium text-white overflow-visible">
					<img
							class="object-cover rounded-l-lg"
							src="{{ $hostage->getPrimaryImage()->url }}"
							alt="">
				</div>
				<div class="flex flex-1 items-center justify-between overflow-visible rounded-r-md border-t border-r border-b border-gray-200 bg-white dark:border-white/10 dark:bg-gray-800/50">
					<div class="flex-1 truncate px-4 py-2 text-sm">
						<a
								href="#"
								class="font-medium text-gray-900 hover:text-gray-600 dark:text-white dark:hover:text-gray-200">{{ $hostage->name }}</a>
						<p class="text-gray-500 dark:text-gray-400 text-xs">{{ $hostage->age }} year
						                                                                        old {{ $hostage->gender->label() }}</p>
					</div>
					<div class="shrink-0 pr-2 overflow-visible">
						<x-dropdown
								class="z-[100]"
								icon="ellipsis-vertical"
								static>
							<x-dropdown.items
									wire:navigate.hover
									href="{{ route('hostage.edit', [
											'hostage' => $hostage,
											'negotiation' => $negotiation ?? null,
											'tenantSubdomain' => tenant()->subdomain
										]) }}"
									text="Edit" />
						</x-dropdown>
					</div>
				</div>
			</li>
		@empty
			<li class="col-span-2 text-center p-4 text-gray-500">
				No hostages found for this negotiation.
				<p class="mt-2">
					<a href="{{ route('hostage.create', ['negotiation' => $negotiation->id, 'tenantSubdomain' => tenant()->subdomain]) }}" class="text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
						Click here to add a new hostage.
					</a>
				</p>
			</li>
		@endforelse
	</ul>
</div>
