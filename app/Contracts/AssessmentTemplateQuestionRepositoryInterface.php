<?php

namespace App\Contracts;

interface AssessmentTemplateQuestionRepositoryInterface
{
    public function createAssessmentTemplateQuestion($data);

    public function getAssessmentTemplateQuestion($id);

    public function getAssessmentTemplateQuestions();

    public function updateAssessmentTemplateQuestion($id, $data);

    public function deleteAssessmentTemplateQuestion($id);
}
