<?php

use App\Events\Subject\SubjectUpdatedEvent;
use App\Models\Subject;
use App\Services\Subject\SubjectFetchingService;
use Livewire\Volt\Component;

new class extends Component {
    public Subject $subject;

    public string $risksText = '';

    public function mount(int $subjectId): void
    {
        $this->subject = app(SubjectFetchingService::class)->fetchSubjectById($subjectId);
        $risks = (array) ($this->subject->risk_factors ?? []);
        $this->risksText = implode("\n", array_filter(array_map('strval', $risks)));
    }

    protected function parseRisks(string $text): array
    {
        $parts = preg_split('/[\n,]+/', $text) ?: [];
        return collect($parts)
            ->map(fn($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    public function save(): void
    {
        $risks = $this->parseRisks($this->risksText);
        $this->subject->risk_factors = $risks;
        $this->subject->save();

        event(new SubjectUpdatedEvent($this->subject->id));
    }
};

?>

<div class="space-y-4">
    <x-textarea label="Risks (one per line or comma separated)" wire:model.defer="risksText" rows="6"/>

    <div class="flex justify-end gap-2">
        <x-button wire:click="save">Save</x-button>
    </div>
</div>
