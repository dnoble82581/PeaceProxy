<?php

namespace App\Services\AssessmentQuestionsAnswer;

use App\Contracts\AssessmentQuestionsAnswerRepositoryInterface;
use App\DTOs\AssessmentQuestionsAnswer\AssessmentQuestionsAnswerDTO;

class AssessmentQuestionsAnswerCreationService
{
    protected AssessmentQuestionsAnswerRepositoryInterface $answerRepository;

    public function __construct(AssessmentQuestionsAnswerRepositoryInterface $answerRepository)
    {
        $this->answerRepository = $answerRepository;
    }

    public function createAssessmentQuestionsAnswer(AssessmentQuestionsAnswerDTO $answerDTO)
    {
        return $this->answerRepository->createAssessmentQuestionsAnswer($answerDTO->toArray());
    }
}
