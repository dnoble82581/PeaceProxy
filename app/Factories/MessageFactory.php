<?php

namespace App\Factories;

use Illuminate\Database\Eloquent\Model;

class MessageFactory
{
    public function generateMessage(Model $model, string $eventName): string
    {
        // Default message fallback
        $message = 'An event occurred.';

        // Handle message generation based on model type and event
        switch (get_class($model)) {
            case \App\Models\Warning::class:
                $message = $this->handleWarning($model, $eventName);
                break;

            case \App\Models\Objective::class:
                $message = $this->handleObjective($model, $eventName);
                break;

            case \App\Models\Warrant::class:
                $message = $this->handleWarrant($model, $eventName);
                break;

            case \App\Models\Demand::class:
                $message = $this->handleDemand($model, $eventName);
                break;
        }

        return $message;
    }

    protected function handleWarning(\App\Models\Warning $warning, string $eventName): string
    {
        $warningLabel = $warning->warning_type?->label() ?? 'Unknown';
        $subjectName = $warning->subject->name ?? 'the subject';
        $createdById = $warning->createdBy->id ?? null;
        $createdByName = $warning->createdBy->name ?? 'Unknown User';

        switch ($eventName) {
            case 'WarningUpdated':
                if ($createdById !== null && $createdById === auth()->id()) {
                    return "You updated a warning for {$subjectName}.";
                } else {
                    return "{$createdByName} updated a warning for {$subjectName}.";
                }
                // no break
            case 'WarningCreated':
                if ($createdById !== null && $createdById === auth()->id()) {
                    return "You created a new {$warningLabel} warning for {$subjectName}.";
                } else {
                    return "{$createdByName} created a new {$warningLabel} warning for {$subjectName}.";
                }
                // no break
            default:
                return "{$createdByName} created a {$warningLabel} warning for {$subjectName}.";
        }

    }

    protected function handleObjective(\App\Models\Objective $objective, string $eventName): string
    {
        $objectiveLabel = $objective->priority->label();

        switch ($eventName) {
            case 'ObjectiveCreated':
                if ($objective->createdBy->id === auth()->id()) {
                    return "You created a new {$objectiveLabel} priority objective.";
                } else {
                    return "{$objective->createdBy->name} created a new {$objectiveLabel} priority objective.";
                }
                // no break
            case 'ObjectiveUpdated':
                return 'An objective has been updated.';
            default:
                return 'An unknown objective event occurred';
        }
    }

    protected function handleWarrant(\App\Models\Warrant $warrant, string $eventName): string
    {
        $warrantLabel = $warrant->type->label();
        $subjectName = $warrant->subject->name;

        switch ($eventName) {
            case 'WarrantUpdated':
                if ($warrant->createdBy->id === auth()->id()) {
                    return "You updated a {$warrantLabel} warrant for {$subjectName}.";
                } else {
                    return "{$warrant->createdBy->name} updated a {$warrantLabel} warrant for {$subjectName}.";
                }
                // no break
            case 'WarrantCreated':
                if ($warrant->createdBy->id === auth()->id()) {
                    return "You created a new {$warrantLabel} warrant for {$subjectName}.";
                } else {
                    return "{$warrant->createdBy->name} created a new {$warrantLabel} warrant for {$subjectName}.";
                }
                // no break
            default:
                $createdByName = $warrant->createdBy->name ?? 'Unknown User';

                return "{$createdByName} created a {$warrantLabel} warrant for {$subjectName}.";
        }
    }

    protected function handleDemand(\App\Models\Demand $demand, string $eventName): string
    {
        $demandLabel = $demand->title;

        switch ($eventName) {
            case 'DemandCreated':
                if ($demand->createdBy->id === auth()->id()) {
                    return "You created a new {$demandLabel} demand.";
                } else {
                    return "{$demand->createdBy->name} created a new {$demandLabel} demand.";
                }
                // no break
            case 'DemandUpdated':
                $title = $demand->title ?? 'a demand';
                $subjectName = $demand->subject->name ?? 'the subject';
                if ($demand->createdBy && $demand->createdBy->id === auth()->id()) {
                    return "A demand that you created for {$subjectName} titled '{$title}' has been updated successfully.";
                } elseif ($demand->createdBy) {
                    return "{$demand->createdBy->name} updated the '{$title}' demand for {$subjectName}.";
                } else {
                    return "The '{$title}' demand was updated for {$subjectName}.";
                }
                // no break
            default:
                return 'An unknown demand event occurred';
        }
    }
}
