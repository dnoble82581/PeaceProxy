<?php

	use App\Factories\MessageFactory;
	use App\Models\Subject;
	use App\Services\Subject\SubjectFetchingService;
	use App\Support\EventNames\SubjectEventNames;
	use Livewire\Volt\Component;
	use TallStackUi\Traits\Interactions;

	new class extends Component {
		use Interactions;

		public Subject $subject;

		// Slide-over state
		public bool $showEditMentalHealthHistoryModal = false;
		public bool $showEditSubstanceAbuseHistoryModal = false;
		public bool $showEditCriminalHistoryModal = false;
		public bool $showEditNotesModal = false;

		public function mount($subjectId):void
		{
			$this->subject = app(SubjectFetchingService::class)->fetchSubjectById($subjectId);
		}

		public function getListeners():array
		{
			$subjectId = $this->subject?->id;
			if (!$subjectId) {
				return [];
			}
			return [
				'echo-private:'.\App\Support\Channels\Subject::subject($subjectId).',.'.SubjectEventNames::SUBJECT_UPDATED => 'handleSubjectUpdated',
			];
		}

		public function handleSubjectUpdated(array $event):void
		{
			// Refresh the subject data so all viewers see the latest content
			$this->subject->refresh();

			// Close any open modals
			$this->showEditMentalHealthHistoryModal = false;
			$this->showEditSubstanceAbuseHistoryModal = false;
			$this->showEditCriminalHistoryModal = false;
			$this->showEditNotesModal = false;

			// Optional: toast informing the user
			$messageFactory = app(MessageFactory::class);
			$message = $messageFactory->generateMessage($this->subject, 'SubjectEdited');
			$this->toast()->timeout()->info($message)->send();
		}

		public function editMentalHealthHistory():void
		{
			$this->showEditMentalHealthHistoryModal = true;
		}

		public function editSubstanceAbuseHistory():void
		{
			$this->showEditSubstanceAbuseHistoryModal = true;
		}

		public function editCriminalHistory():void
		{
			$this->showEditCriminalHistoryModal = true;
		}

		public function editNotes():void
		{
			$this->showEditNotesModal = true;
		}
	}

?>

<div class="p-4 grid grid-cols-1 gap-4 sm:grid-cols-2">

	<x-card>
		<x-slot:header>
			<div class="flex justify-between pr-4">
				<h3 class="p-2">
					Mental Health History
				</h3>
				<x-button
						flat
						sm
						icon="pencil-square"
						wire:click="editMentalHealthHistory" />
			</div>

		</x-slot:header>
		<p class="text-xs">{{ $subject->mental_health_history ?? 'No Known Mental Health History' }}</p>
	</x-card>

	<x-card>
		<x-slot:header>
			<div class="flex justify-between pr-4">
				<h3 class="p-2">
					Substance Abuse History
				</h3>
				<x-button
						flat
						sm
						icon="pencil-square"
						wire:click="editSubstanceAbuseHistory" />
			</div>
		</x-slot:header>
		<p class="text-xs">{{ $subject->substance_abuse_history ?? 'No Known Substance Abuse History'}}</p>
	</x-card>

	<x-card>
		<x-slot:header>
			<div class="flex justify-between pr-4">
				<h3 class="p-2">
					Criminal History
				</h3>
				<x-button
						flat
						sm
						icon="pencil-square"
						wire:click="editCriminalHistory" />
			</div>
		</x-slot:header>
		<p class="text-xs">{{ $subject->criminal_history ?? 'No Known Criminal History' }}</p>
	</x-card>

	<x-card>
		<x-slot:header>
			<div class="flex justify-between pr-4">
				<h3 class="p-2">
					Notes
				</h3>
				<x-button
						flat
						sm
						icon="pencil-square"
						wire:click="editNotes" />
			</div>
		</x-slot:header>
		<p class="text-xs">{{ $subject->notes ?? 'No Notes Currently' }}</p>
	</x-card>

	<!-- Slides for quick edits -->
	<x-slide
			title="Edit Mental Health History"
			wire="showEditMentalHealthHistoryModal">
		<livewire:forms.subject.edit-subject-mental-health-history :subjectId="$subject->id" />
	</x-slide>
	<x-slide
			title="Edit Substance Abuse History"
			wire="showEditSubstanceAbuseHistoryModal">
		<livewire:forms.subject.edit-subject-substance-abuse-history :subjectId="$subject->id" />
	</x-slide>
	<x-slide
			title="Edit Criminal History"
			wire="showEditCriminalHistoryModal">
		<livewire:forms.subject.edit-subject-criminal-history :subjectId="$subject->id" />
	</x-slide>
	<x-slide
			title="Edit Notes"
			wire="showEditNotesModal">
		<livewire:forms.subject.edit-subject-notes :subjectId="$subject->id" />
	</x-slide>

</div>
