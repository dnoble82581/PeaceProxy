<?php

namespace App\Services\AssessmentQuestionsAnswer;

use App\Contracts\AssessmentQuestionsAnswerRepositoryInterface;

class AssessmentQuestionsAnswerDestructionService
{
    protected AssessmentQuestionsAnswerRepositoryInterface $answerRepository;

    public function __construct(AssessmentQuestionsAnswerRepositoryInterface $answerRepository)
    {
        $this->answerRepository = $answerRepository;
    }

    public function deleteAssessmentQuestionsAnswer($id)
    {
        return $this->answerRepository->deleteAssessmentQuestionsAnswer($id);
    }
}
