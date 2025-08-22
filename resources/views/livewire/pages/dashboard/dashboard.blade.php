<?php

	use App\Models\Negotiation;
	use App\Models\User;
	use App\Services\Auth\LogoutService;
	use Livewire\Attributes\Computed;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('layouts.app')] class extends Component {
		#[Computed]
		public function active():int
		{
			return User::where('is_active', true)->count();
		}

		#[Computed]
		public function averageDuration():float|int
		{
			return Negotiation::sum('duration_minutes') / Negotiation::count();
		}

		#[Computed]
		public function userCount():int
		{
			return User::count();
		}
	}

?>
<div class="text-2xl dark:text-white grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
	<x-card>
		<x-slot:header>
			<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">Negotiations</h4>
		</x-slot:header>
		<div class="space-y-4 bg-gray-100/60 dark:bg-dark-700 p-4 rounded-lg">
			<div class="flex justify-between text-xs border-b">
				<div>Total</div>
				<div>{{ tenant()->negotiations()->count() }}</div>
			</div>
			<div class="flex justify-between text-xs border-b">
				<div>Active</div>
				<div>{{ tenant()->negotiations()->count() }}</div>
			</div>
			<div class="flex justify-between text-xs border-b">
				<div>Average Duration</div>
				<div>{{ $this->averageDuration() }}m</div>
			</div>
		</div>
	</x-card>
	<x-card>
		<x-slot:header>
			<h4 class="text-lg p-2 bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">Users</h4>
		</x-slot:header>
		<div class="space-y-4 bg-gray-100/60 dark:bg-dark-700 p-4 rounded-lg">
			<div class="flex justify-between text-xs border-b">
				<div>Total</div>
				<div>{{ $this->userCount() }}</div>
			</div>
			<div class="flex justify-between text-xs border-b">
				<div>Active</div>
				<div>{{ $this->active() }}</div>
			</div>
		</div>
	</x-card>

</div>

