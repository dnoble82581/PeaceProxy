<?php

use App\Events\Subject\SubjectUpdatedEvent;
use App\Models\Subject;
use App\Services\Subject\SubjectFetchingService;
use Illuminate\Support\Facades\Validator;
use Livewire\Volt\Component;

new class extends Component {
    public Subject $subject;

    public string $mental_health_history = '';

    public function mount(int $subjectId): void
    {
        $this->subject = app(SubjectFetchingService::class)->fetchSubjectById($subjectId);
        $this->mental_health_history = (string) ($this->subject->mental_health_history ?? '');
    }

    public function save(): void
    {
        $data = [
            'mental_health_history' => $this->mental_health_history,
        ];

        Validator::make($data, [
            'mental_health_history' => ['nullable', 'string', 'max:20000'],
        ])->validate();

        $this->subject->fill($data);
        $this->subject->save();

        event(new SubjectUpdatedEvent($this->subject->id));
    }
};

?>

<div class="space-y-4">
    <x-textarea
        rows="10"
        label="Mental Health History"
        wire:model.defer="mental_health_history" />

    <div class="flex justify-end gap-2">
        <x-button wire:click="save">Save</x-button>
    </div>
</div>
