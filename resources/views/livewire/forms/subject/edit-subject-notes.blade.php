<?php

use App\Events\Subject\SubjectUpdatedEvent;
use App\Models\Subject;
use App\Services\Subject\SubjectFetchingService;
use Illuminate\Support\Facades\Validator;
use Livewire\Volt\Component;

new class extends Component {
    public Subject $subject;

    public string $notes = '';

    public function mount(int $subjectId): void
    {
        $this->subject = app(SubjectFetchingService::class)->fetchSubjectById($subjectId);
        $this->notes = (string) ($this->subject->notes ?? '');
    }

    public function save(): void
    {
        $data = [
            'notes' => $this->notes,
        ];

        Validator::make($data, [
            'notes' => ['nullable', 'string', 'max:20000'],
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
        label="Notes"
        wire:model.defer="notes" />

    <div class="flex justify-end gap-2">
        <x-button wire:click="save">Save</x-button>
    </div>
</div>
