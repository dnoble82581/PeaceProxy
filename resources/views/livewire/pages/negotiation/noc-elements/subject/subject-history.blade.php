<?php

	use App\Models\Subject;
	use App\Services\Subject\SubjectFetchingService;
	use Livewire\Volt\Component;

	new class extends Component {
		public Subject $subject;

		public function mount($subjectId)
		{
			$this->subject = app(SubjectFetchingService::class)->fetchSubjectById($subjectId);
		}
	}

?>

<div class="p-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
	<x-card>
		<x-slot:header>
			<h3 class="p-2">
				Mental Health History
			</h3>
		</x-slot:header>
		<p class="text-xs">{{ $subject->mental_health_history ?? 'No Known Mental Health History' }}</p>
	</x-card>

	<x-card>
		<x-slot:header>
			<h3 class="p-2">
				Substance Abuse History
			</h3>
		</x-slot:header>
		<p class="text-xs">{{ $subject->substance_abuse_history ?? 'No Known Substance Abuse History'}}</p>
	</x-card>

	<x-card>
		<x-slot:header>
			<h3 class="p-2">
				Criminal History
			</h3>
		</x-slot:header>
		<p class="text-xs">{{ $subject->criminal_history ?? 'No Known Criminal History' }}</p>
	</x-card>

	<x-card>
		<x-slot:header>
			<h3 class="p-2">
				Notes
			</h3>
		</x-slot:header>
		<p class="text-xs">{{ $subject->notes ?? 'No Notes Currently' }}</p>
	</x-card>

</div>
