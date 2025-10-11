<?php

	use App\Models\User;
	use Illuminate\Pagination\LengthAwarePaginator;
	use Livewire\Attributes\Computed;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Livewire\WithPagination;
	use TallStackUi\Traits\Interactions;

	new #[Layout('layouts.app'), \Livewire\Attributes\Title('Users - Peace Proxy')] class extends Component {
		use WithPagination;
		use Interactions;

		public ?int $quantity = 10;
		public ?string $search = null;
		public bool $showDeleteModal = false;
		public array $sort = [
			'column' => 'created_at',
			'direction' => 'desc',
		];
		public ?User $userToDelete = null;

		public array $headers = [
			['index' => 'id', 'label' => '#'],
			['index' => 'name', 'label' => 'Name'],
			['index' => 'email', 'label' => 'Email'],
			['index' => 'team', 'label' => 'Team'],
			['index' => 'permissions', 'label' => 'Permissions'],
			['index' => 'created_at', 'label' => 'Created'],
			['index' => 'action', 'sortable' => false, 'label' => 'Actions'],
		];

		#[Computed]
		public function rows():LengthAwarePaginator
		{
			// Cache tenant ID to avoid multiple function calls
			$tenantId = tenant()->id;

			// Trim a search term once if it exists
			$searchTerm = $this->search !== null? trim($this->search) : null;

			return User::query()
				->select([
					'id', 'name', 'email', 'permissions', 'primary_team_id', 'created_at'
				]) // Select only needed fields
				->with('primaryTeam')
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

		public function confirmDelete(User $user):void
		{
			$this->userToDelete = $user;
			$this->showDeleteModal = true;
		}

		public function deleteUser():void
		{
			if ($this->userToDelete) {
				try {
					$this->userToDelete->delete();

					// Refresh the component to update the table
					$this->dispatch('$refresh');

					$this->dialog()
						->success(__('Done!'), __('User deleted successfully.'))
						->send();
				} catch (\Exception $e) {
					$this->dialog()
						->error(__('Error!'), __('Failed to delete user. Please try again.'))
						->send();
				} finally {
					$this->userToDelete = null;
				}
			}
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
			@interact('column_permissions', $row)
			{{ ucfirst($row->permissions) ?: 'None' }}
			@endinteract

			@interact('column_team', $row)
			{{ $row->primaryTeam?->name ?? 'None' }}
			@endinteract

			@interact('column_created_at', $row)
			{{ $row->created_at->diffForHumans() }}
			@endinteract

			@interact('column_action', $row)
			<div class="flex gap-2">
				@can('update', tenant())
					<x-button.circle
							sm
							icon="pencil"
							wire:click="$dispatch('load::user', { 'user' : '{{ $row->id }}'})" />
					@if(authUser()->id === $row->id)
						<x-button.circle
								sm
								disabled
								color="red"
								icon="trash" />
					@else
						<x-button.circle
								sm
								color="red"
								icon="trash"
								wire:click="confirmDelete({{ $row }})" />
					@endif

				@else
					<x-button.circle
							sm
							disabled
							icon="pencil" />
					<x-button.circle
							sm
							disabled
							color="red"
							icon="trash" />
				@endcan

			</div>
			@endinteract
		</x-table>
	</x-card>
	<livewire:users.update @updated="$refresh" />

	<x-modal
			center
			name="delete-user"
			wire="showDeleteModal">
		<x-slot:title>
			Delete User
		</x-slot:title>
		<div class="flex items-center gap-2">
			<x-icon
					class="w-8 h-8 text-red-500"
					name="exclamation-circle" />
			<div>
				<p>Are you sure you want to delete user
					<span class="font-semibold">{{ $userToDelete ? $userToDelete->name : '' }}</span>?
				</p>
				<p class="text-xs dark:text-dark-300 font-semibold">This action cannot be undone.</p>
			</div>
		</div>
		<x-slot:footer>
			<x-button
					sm
					color="secondary"
					wire:click="$set('showDeleteModal', false)">Close
			</x-button>
			<x-button
					sm
					color="red"
					wire:click="deleteUser">Delete
			</x-button>
		</x-slot:footer>
	</x-modal>
</div>
