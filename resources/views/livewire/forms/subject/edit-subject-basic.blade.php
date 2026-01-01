<?php

use App\Events\Subject\SubjectUpdatedEvent;
use App\Models\Subject;
use App\Services\Subject\SubjectFetchingService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Livewire\Volt\Component;

new class extends Component {
    public Subject $subject;

    public string $name = '';
    public ?string $date_of_birth = null;
    public ?string $status = null;

    public function mount(int $subjectId): void
    {
        $this->subject = app(SubjectFetchingService::class)->fetchSubjectById($subjectId);
        $this->name = (string) ($this->subject->name ?? '');
        $this->date_of_birth = optional($this->subject->date_of_birth)->format('Y-m-d');
        $this->status = $this->subject->status?->value;
    }

    public function save(): void
    {
        $data = [
            'name' => $this->name,
            'date_of_birth' => $this->date_of_birth,
            'status' => $this->status,
        ];

        Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
        ])->validate();

        $this->subject->fill([
            'name' => $data['name'],
            'date_of_birth' => $data['date_of_birth'],
            'status' => $data['status'],
        ]);
        $this->subject->save();

        event(new SubjectUpdatedEvent($this->subject->id));
    }
};

?>

<div class="space-y-4">
    <x-input label="Name" wire:model.defer="name" />
    <x-input type="date" label="Date of Birth" wire:model.defer="date_of_birth" />
    <x-input label="Status" wire:model.defer="status" />

    <div class="flex justify-end gap-2">
        <x-button wire:click="save">Save</x-button>
    </div>
</div>
