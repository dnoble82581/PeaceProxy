<?php

	use App\Models\Negotiation;
	use App\Models\User;
	use App\Services\Auth\LogoutService;
	use Livewire\Attributes\Computed;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\DB;

	new #[Layout('layouts.app')] class extends Component {
		public $stats;
		
		public function mount()
		{
			// Get tenant ID once
			$tenantId = tenant()->id;
			
			// Get all user stats in a single query
			$userStats = User::selectRaw('COUNT(*) as total, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
				->where('tenant_id', $tenantId)
				->first();
				
			// Get negotiation stats in a single query
			$negotiationStats = Negotiation::selectRaw('
				COUNT(*) as total, 
				COUNT(CASE WHEN status = "active" THEN 1 END) as active,
				CASE WHEN COUNT(*) > 0 THEN SUM(duration_minutes) / COUNT(*) ELSE 0 END as avg_duration
			')
			->where('tenant_id', $tenantId)
			->first();
			
			// Store all stats in a single property
			$this->stats = [
				'users' => [
					'total' => $userStats->total ?? 0,
					'active' => $userStats->active ?? 0,
				],
				'negotiations' => [
					'total' => $negotiationStats->total ?? 0,
					'active' => $negotiationStats->active ?? 0,
					'avgDuration' => $negotiationStats->avg_duration ?? 0,
				],
			];
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
				<div>{{ $stats['negotiations']['total'] }}</div>
			</div>
			<div class="flex justify-between text-xs border-b">
				<div>Active</div>
				<div>{{ $stats['negotiations']['active'] }}</div>
			</div>
			<div class="flex justify-between text-xs border-b">
				<div>Average Duration</div>
				<div>{{ $stats['negotiations']['avgDuration'] }}m</div>
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
				<div>{{ $stats['users']['total'] }}</div>
			</div>
			<div class="flex justify-between text-xs border-b">
				<div>Active</div>
				<div>{{ $stats['users']['active'] }}</div>
			</div>
		</div>
	</x-card>

</div>

