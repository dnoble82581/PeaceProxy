<?php

	use App\Models\User;
	use Illuminate\Pagination\LengthAwarePaginator;
	use Livewire\Attributes\Computed;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\On;
	use Livewire\Attributes\Title;
	use Livewire\Volt\Component;
	use Livewire\WithPagination;
	use TallStackUi\Traits\Interactions;

	new #[Layout('components.layouts.app'), Title('Users - Peace Proxy')] class extends Component {
		use WithPagination;
		use Interactions;

		public ?int $quantity = 10;
		public ?string $search = null;
		public bool $showDeleteModal = false;
		public bool $showCreateModal = false;
		public bool $showUpdateModal = false;
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

			$query = User::query()
				->select([
					'users.id',
					'users.name',
					'users.email',
					'users.permissions',
					'users.primary_team_id',
					'users.created_at',
				]) // Select only needed fields (qualified to avoid ambiguity when joining)
				->with('primaryTeam')
				->where('users.tenant_id', $tenantId)
				->when($searchTerm, function ($query) use ($searchTerm) {
					$query->where(function ($q) use ($searchTerm) {
						$q->where('users.name', 'like', '%'.$searchTerm.'%')
							->orWhere('users.email', 'like', '%'.$searchTerm.'%');
					});
				});

			// Handle sorting, including sorting by related team name
			if (($this->sort['column'] ?? null) === 'team') {
				$query->leftJoin('teams', 'teams.id', '=', 'users.primary_team_id')
					->orderBy('teams.name', $this->sort['direction'] ?? 'asc')
					->orderBy('users.id'); // Deterministic ordering
			} else {
				$query->orderBy($this->sort['column'] ?? 'created_at', $this->sort['direction'] ?? 'desc');
			}

			return $query
				->paginate($this->quantity)
				->withQueryString();
		}

		#[On('close-modals')]
		public function closeModals()
		{
			$this->showCreateModal = false;
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

					$this->toast()
						->success(__('Done!'), __('User deleted successfully.'))
						->send();
				} catch (Exception $e) {
					$this->toast()
						->error(__('Error!'), __('Failed to delete user. Please try again.'))
						->send();
				} finally {
					$this->userToDelete = null;
				}
			}
			$this->showDeleteModal = false;
		}
	}
?>

<div class="p-4">
	<x-card>
		<x-slot:header>
			<div class="flex items-center justify-between py-2 px-4 bg-gray-200/50 dark:text-white dark:bg-dark-800/50 rounded-t-lg">
				<h4 class="text-lg">Manage Users</h4>
				<div>
					<x-button
							icon="plus"
							wire:click="$toggle('showCreateModal')" />
				</div>
			</div>
		</x-slot:header>

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
			{{ $row->created_at?->diffForHumans() ?? '' }}
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


	<livewire:users.update-user @updated="$refresh" />

	<x-modal
			wire="showCreateModal"
			name="create-user">
		<x-slot:title>
			Create New User
		</x-slot:title>
		<livewire:users.create-user />
	</x-modal>

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
