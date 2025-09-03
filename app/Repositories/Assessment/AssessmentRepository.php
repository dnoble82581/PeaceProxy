<?php

namespace App\Repositories\Assessment;

use App\Contracts\AssessmentRepositoryInterface;
use App\Models\Assessment;
use Illuminate\Database\Eloquent\Collection;

class AssessmentRepository implements AssessmentRepositoryInterface
{
    public function createAssessment($data)
    {
        return Assessment::create($data);
    }

    public function getAssessments(): Collection
    {
        return Assessment::all();
    }

    public function updateAssessment($id, $data)
    {
        $assessment = $this->getAssessment($id);
        $assessment->update($data);
        return $assessment;
    }

    public function getAssessment($id)
    {
        return Assessment::find($id);
    }

    public function deleteAssessment($id)
    {
        $assessment = $this->getAssessment($id);
        $assessment->delete();
        return $assessment;
    }
}
