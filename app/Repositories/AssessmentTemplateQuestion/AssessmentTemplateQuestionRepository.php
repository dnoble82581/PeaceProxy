<?php

namespace App\Repositories\AssessmentTemplateQuestion;

use App\Contracts\AssessmentTemplateQuestionRepositoryInterface;
use App\Models\AssessmentTemplateQuestion;
use Illuminate\Database\Eloquent\Collection;

class AssessmentTemplateQuestionRepository implements AssessmentTemplateQuestionRepositoryInterface
{
    public function createAssessmentTemplateQuestion($data)
    {
        return AssessmentTemplateQuestion::create($data);
    }

    public function getAssessmentTemplateQuestions(): Collection
    {
        return AssessmentTemplateQuestion::all();
    }

    public function updateAssessmentTemplateQuestion($id, $data)
    {
        $question = $this->getAssessmentTemplateQuestion($id);
        $question->update($data);
        return $question;
    }

    public function getAssessmentTemplateQuestion($id)
    {
        return AssessmentTemplateQuestion::find($id);
    }

    public function deleteAssessmentTemplateQuestion($id)
    {
        $question = $this->getAssessmentTemplateQuestion($id);
        $question->delete();
        return $question;
    }
}
