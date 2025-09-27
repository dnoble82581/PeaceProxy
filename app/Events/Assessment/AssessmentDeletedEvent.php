<?php

namespace App\Events\Assessment;

use App\Support\Channels\Subject;
use App\Support\EventNames\SubjectEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssessmentDeletedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public array $data)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Subject::subjectAssessment($this->data['subjectId'])),
        ];
    }

    public function broadcastAs()
    {
        return SubjectEventNames::ASSESSMENT_DELETED;
    }

    public function broadcastWith()
    {
        return [
            'assessmentId' => $this->data['assessmentId'],
            'subjectId' => $this->data['subjectId'],
        ];
    }
}
