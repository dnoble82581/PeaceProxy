<?php

	use App\Models\User;
	use Illuminate\Pagination\LengthAwarePaginator;
	use Livewire\Attributes\Computed;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Livewire\WithPagination;

	new #[Layout('layouts.app')] class extends Component {
		use WithPagination;

		public ?int $quantity = 10;
		public ?string $search = null;
		public array $sort = [
			'column' => 'created_at',
			'direction' => 'desc',
		];

		public array $headers = [
			['index' => 'id', 'label' => '#'],
			['index' => 'name', 'label' => 'Name'],
			['index' => 'email', 'label' => 'Email'],
			['index' => 'created_at', 'label' => 'Created'],
			['index' => 'action', 'sortable' => false, 'label' => 'Actions'],
		];

 	#[Computed]
 	public function rows():LengthAwarePaginator
 	{
 		// Cache tenant ID to avoid multiple function calls
 		$tenantId = tenant()->id;
		
 		// Trim search term once if it exists
 		$searchTerm = $this->search !== null ? trim($this->search) : null;
		
 		return User::query()
 			->select(['id', 'name', 'email', 'created_at']) // Select only needed fields
 			->where('tenant_id', $tenantId)
 			->when($searchTerm, function ($query) use ($searchTerm) {
 				$query->where(function ($q) use ($searchTerm) {
 					$q->where('name', 'like', '%'.$searchTerm.'%')
 						->orWhere('email', 'like', '%'.$searchTerm.'%');
 				});
 			})
 			->orderBy(...array_values($this->sort))
 			->paginate($this->quantity)
 			->withQueryString();
 	}
	}

?>

<div class="p-4">
	<x-card>
		<x-slot:header>
			<h4 class="text-lg p-2 bg-gray-200/50 dark:text-white dark:bg-dark-800/50 rounded-t-lg">Manage Users</h4>
		</x-slot:header>

		<div class="mb-4 mt-4">
			<livewire:users.create @created="$refresh" />
		</div>

		<x-table
				:$headers
				:$sort
				:rows="$this->rows"
				paginate
				simple-pagination
				filter
				loading
				:quantity="[5, 10, 25, 50]">
			@interact('column_created_at', $row)
			{{ $row->created_at->diffForHumans() }}
			@endinteract

			@interact('column_action', $row)
			<div class="flex gap-1">
				<x-button.circle
						icon="pencil"
						wire:click="$dispatch('load::user', { 'user' : '{{ $row->id }}'})" />
				<livewire:users.delete
						:user="$row"
						:key="uniqid('', true)"
						@deleted="$refresh" />
			</div>
			@endinteract
		</x-table>
	</x-card>

	<livewire:users.update @updated="$refresh" />
</div>
