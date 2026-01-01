<?php

use App\Events\Subject\SubjectUpdatedEvent;
use App\Models\Subject;
use App\Services\Subject\SubjectFetchingService;
use Livewire\Volt\Component;

new class extends Component {
    public Subject $subject;

    public string $aliasesText = '';

    public function mount(int $subjectId): void
    {
        $this->subject = app(SubjectFetchingService::class)->fetchSubjectById($subjectId);
        $aliases = (array) ($this->subject->alias ?? []);
        $this->aliasesText = implode(", ", array_filter(array_map('strval', $aliases)));
    }

    protected function parseAliases(string $text): array
    {
        $parts = preg_split('/[,\n]+/', $text) ?: [];
        $clean = collect($parts)
            ->map(fn($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        return $clean;
    }

    public function save(): void
    {
        $aliases = $this->parseAliases($this->aliasesText);
        $this->subject->alias = $aliases;
        $this->subject->save();

        event(new SubjectUpdatedEvent($this->subject->id));
    }
};

?>

<div class="space-y-4">
    <x-textarea label="Aliases (comma or newline separated)" wire:model.defer="aliasesText" rows="5"/>

    <div class="flex justify-end gap-2">
        <x-button wire:click="save">Save</x-button>
    </div>
</div>
