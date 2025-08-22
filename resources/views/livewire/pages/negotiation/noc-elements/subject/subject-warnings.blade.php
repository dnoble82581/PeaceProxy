<?php

	use App\Models\Subject;
	use App\Models\User;
	use App\Enums\General\RiskLevels;
	use App\Services\Subject\SubjectFetchingService;
	use App\Services\Warning\WarningDeletionService;
	use App\Services\Warning\WarningFetchingService;
	use Carbon\Carbon;
	use Livewire\Volt\Component;

	new class extends Component {
		public Subject $subject;
		public int $negotiationId;

		public function mount($subjectId, $negotiationId)
		{
			$this->subject = $this->fetchSubject($subjectId);
			$this->negotiationId = $negotiationId;
		}

		private function fetchSubject($subjectId)
		{
			return app(SubjectFetchingService::class)->fetchSubjectById($subjectId);
		}

		public function getWarningsProperty()
		{
			return app(WarningFetchingService::class)->fetchWarningsBySubject($this->subject);
		}

		public function deleteWarning($warningId):void
		{
			app(WarningDeletionService::class)->deleteWarning($warningId);
			$this->dispatch('warning-deleted');
		}


		public function getUserName($userId):string
		{
			$user = User::find($userId);
			return $user? $user->name : 'Unknown User';
		}

		public function getTimeAgo($createdAt):string
		{
			return Carbon::parse($createdAt)->diffForHumans();
		}
	}

?>
<div>
	<div class="text-right px-4 pt-1">
		<x-button
				wire:navigate.hover
				href="{{ route('warning.create', ['tenantSubdomain' => tenant()->subdomain, 'negotiationId' => $negotiationId, 'subjectId' => $subject->id]) }}"
				color=""
				flat
				sm
				icon="plus"
		/>
	</div>
	<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4">
		@forelse($this->warnings as $warning)
			<x-card>
				<x-slot:header>
					<div class="flex items-center justify-between p-2">
						<div>
							<p class="font-semibold text-sm">{{ $warning->warning_type->label() }}</p>
						</div>
						<div>
							<x-badge
									xs
									:color="$warning->risk_level->color()"
									:text="$warning->risk_level->label()" />
						</div>
					</div>
				</x-slot:header>
				<div>
					<p class="text-xs">
						{{ $warning->warning }}
					</p>

					<p class="text-right text-[10px] mt-2">
						Created
						by {{ $this->getUserName($warning->created_by_id) }} {{ $this->getTimeAgo($warning->created_at) }}
						.
					</p>

					<div class="flex justify-end mt-2 space-x-2">
						<x-button.circle
								wire:navigate.hover
								href="{{ route('warning.edit', ['tenantSubdomain' => tenant()->subdomain, 'negotiationId' => $negotiationId, 'subjectId' => $subject->id, 'warningId' => $warning->id]) }}"
								flat
								color="sky"
								icon="pencil-square"
								sm />
						<x-button.circle
								wire:click="deleteWarning({{ $warning->id }})"
								flat
								color="red"
								icon="trash"
								sm />
					</div>
				</div>
			</x-card>
		@empty
			<div class="col-span-2 text-center p-4 text-gray-500">
				No warnings found for this subject.
			</div>
		@endforelse
	</div>
</div>

