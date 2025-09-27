<?php

namespace App\Events\Assessment;

use App\Support\Channels\Subject;
use App\Support\EventNames\SubjectEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssessmentCompletedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $subjectId, public int $assessmentId)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Subject::subjectAssessment($this->subjectId)),
        ];
    }

    public function broadcastAs(): string
    {
        return SubjectEventNames::ASSESSMENT_COMPLETED;
    }

    public function broadcastWith()
    {
        return [
            'assessmentId' => $this->assessmentId,
            'subjectId' => $this->subjectId,
        ];
    }
}
