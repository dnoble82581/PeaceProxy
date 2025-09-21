<?php

use App\Models\Negotiation;
use App\Models\Subject;
use Livewire\Volt\Component;

new class extends Component {
    public Negotiation $negotiation;
    public ?Subject $primarySubject = null;
    public string $armedStatus = 'Unknown';

    public function mount($negotiation): void
    {
        $this->negotiation = $negotiation;
        $this->primarySubject = $negotiation?->primarySubject();
        $this->armedStatus = $this->determineArmedStatus();
    }

    private function determineArmedStatus(): string
    {
        try {
            $subject = $this->primarySubject;
            if (!$subject) return 'Unknown';

            $attrs = $subject->getAttributes();
            if (array_key_exists('is_armed', $attrs)) {
                return $attrs['is_armed'] ? 'Yes' : 'No';
            }
            if (array_key_exists('armed', $attrs)) {
                return (bool) $attrs['armed'] ? 'Yes' : 'No';
            }

            $risk = $subject->risk_factors ?? null;
            if (is_array($risk)) {
                if ((isset($risk['armed']) && (bool) $risk['armed'] === true) || in_array('armed', array_map('strtolower', array_keys($risk)))) {
                    return 'Yes';
                }
                if (in_array('unarmed', array_map('strtolower', array_keys($risk)))) {
                    return 'No';
                }
                if (in_array('armed', array_map('strtolower', $risk), true)) {
                    return 'Yes';
                }
            }

            $tags = $this->negotiation->tags ?? [];
            if (is_array($tags) && in_array('armed', array_map('strtolower', $tags), true)) {
                return 'Yes';
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return 'Unknown';
    }
};

?>

<x-card class="h-full">
    <x-slot:header>
        <div class="p-4 flex items-center justify-between">
            <h3>Case #: <span class="font-semibold">{{ $negotiation->case_number ?? '—' }}</span></h3>
            <x-badge :text="$armedStatus === 'Yes' ? 'Armed' : ($armedStatus === 'No' ? 'Unarmed' : 'Unknown')" :color="$armedStatus === 'Yes' ? 'rose' : ($armedStatus === 'No' ? 'teal' : 'slate')" />
        </div>
    </x-slot:header>
    <div class="space-y-3">
        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-300">Situation</div>
            <div class="text-sm text-gray-900 dark:text-dark-50">{{ $negotiation->summary ?? 'Awaiting more information' }}</div>
        </div>
        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-300">Description</div>
            <div class="text-sm text-gray-900 dark:text-dark-50">{{ $negotiation->initial_complaint }}</div>
        </div>
    </div>
</x-card>
