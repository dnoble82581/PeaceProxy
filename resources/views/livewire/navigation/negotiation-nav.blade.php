<?php

	use App\Models\Negotiation;
	use App\Services\NegotiationUser\NegotiationUserUpdatingService;
	use Illuminate\Routing\Redirector;
	use Livewire\Volt\Component;
	use Carbon\Carbon;

	new class extends Component {
		public ?Negotiation $negotiation = null;

		public function mount($negotiation = null)
		{
			$this->negotiation = $negotiation;
		}

		public function leaveNegotiation():Redirector
		{
			if ($this->negotiation) {
				try {
					$user = $this->negotiation->users()->where('user_id', authUser()->id)->where('left_at',
						null)->first();

					if ($user && $user->pivot) {
						app(NegotiationUserUpdatingService::class)->updateLeftAtForUser(
							$this->negotiation->id, $user->pivot->role->value
						);
					}
				} catch (Exception $e) {
					// Log the error or handle it as needed
					// You can add a flash message here if you want to inform the user
				}
			}

			return redirect(route('dashboard.negotiations', tenant()->subdomain));
		}

		public function endNegotiation():Redirector
		{
			if ($this->negotiation) {
				try {
					// Update ended_at if it's not already set
					if (is_null($this->negotiation->ended_at)) {
						$now = now();

						// Calculate duration in minutes if started_at is set
						$durationInMinutes = null;
						if ($this->negotiation->started_at) {
							$startedAt = Carbon::parse($this->negotiation->started_at);
							$durationInMinutes = $startedAt->diffInMinutes($now);
						}

						// Update the negotiation
						$this->negotiation->update([
							'ended_at' => $now,
							'duration' => $durationInMinutes
						]);
					}

					// Also mark all users as having left the negotiation
					$this->negotiation->users()
						->wherePivot('left_at', null)
						->each(function ($user) {
							if ($user->pivot) {
								app(NegotiationUserUpdatingService::class)->updateLeftAtForUser(
									$this->negotiation->id, $user->pivot->role->value
								);
							}
						});
				} catch (Exception $e) {
					// Log the error or handle it as needed
				}
			}

			return redirect(route('dashboard.negotiations', tenant()->subdomain));
		}
	}

?>

<div class="flex items-center justify-between py-2 px-8 dark:bg-dark-700 mb-4">
	<div class="flex items-center gap-4">
		<div>
			<x-avatar image="{{ authUser()->avatar_path}}" />
		</div>
		<div>
			<p class="text-sm font-bold">{{ authUser()->name }}</p>
			@if($this->negotiation)
				<p class="text-xs">{{ authUserRole($this->negotiation)->label() }}</p>
			@else
				<p class="text-xs">User</p>
			@endif
		</div>

	</div>
	<div
			class="flex items-center gap-2">
		<x-theme-switch
				@click="alert('clicked')"
				only-icons />
		<x-dropdown>
			<x-slot:action>
				<x-button
						color=""
						icon="cog-6-tooth"
						flat="true"
						x-on:click="show = !show">
				</x-button>
			</x-slot:action>

			<x-dropdown.items
					href="{{ route('dashboard.settings', ['tenantSubdomain' => tenant()->subdomain, 'tab' => 'profile']) }}"
					icon="cog"
					text="Settings" />
			<x-dropdown.items
					wire:click="leaveNegotiation"
					icon="arrow-long-left"
					text="Dashboard" />
			<x-dropdown.items
					wire:click="endNegotiation"
					icon="archive-box-arrow-down"
					text="End Negotiation" />
			<form
					method="POST"
					action="{{ route('logout') }}">
				@csrf
				<x-dropdown.items
						separator
						icon="arrow-left-start-on-rectangle"
						text="Logout"
						onclick="event.preventDefault(); this.closest('form').submit();" />
			</form>

		</x-dropdown>
	</div>
</div>
