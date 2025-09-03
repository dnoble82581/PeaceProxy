<?php

namespace App\Repositories\AssessmentQuestionsAnswer;

use App\Contracts\AssessmentQuestionsAnswerRepositoryInterface;
use App\Models\AssessmentQuestionsAnswer;
use Illuminate\Database\Eloquent\Collection;

class AssessmentQuestionsAnswerRepository implements AssessmentQuestionsAnswerRepositoryInterface
{
    public function createAssessmentQuestionsAnswer($data)
    {
        return AssessmentQuestionsAnswer::create($data);
    }

    public function getAssessmentQuestionsAnswers(): Collection
    {
        return AssessmentQuestionsAnswer::all();
    }

    public function updateAssessmentQuestionsAnswer($id, $data)
    {
        $answer = $this->getAssessmentQuestionsAnswer($id);
        $answer->update($data);
        return $answer;
    }

    public function getAssessmentQuestionsAnswer($id)
    {
        return AssessmentQuestionsAnswer::find($id);
    }

    public function deleteAssessmentQuestionsAnswer($id)
    {
        $answer = $this->getAssessmentQuestionsAnswer($id);
        $answer->delete();
        return $answer;
    }
}
