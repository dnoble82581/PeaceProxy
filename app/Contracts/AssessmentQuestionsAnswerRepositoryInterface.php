<?php

namespace App\Contracts;

interface AssessmentQuestionsAnswerRepositoryInterface
{
    public function createAssessmentQuestionsAnswer($data);

    public function getAssessmentQuestionsAnswer($id);

    public function getAssessmentQuestionsAnswers();

    public function updateAssessmentQuestionsAnswer($id, $data);

    public function deleteAssessmentQuestionsAnswer($id);
}
