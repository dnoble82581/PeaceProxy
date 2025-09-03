<?php

namespace App\Services\AssessmentQuestionsAnswer;

use App\Contracts\AssessmentQuestionsAnswerRepositoryInterface;
use App\DTOs\AssessmentQuestionsAnswer\AssessmentQuestionsAnswerDTO;

class AssessmentQuestionsAnswerFetchingService
{
    protected AssessmentQuestionsAnswerRepositoryInterface $answerRepository;

    public function __construct(AssessmentQuestionsAnswerRepositoryInterface $answerRepository)
    {
        $this->answerRepository = $answerRepository;
    }

    public function getAssessmentQuestionsAnswer($id)
    {
        return $this->answerRepository->getAssessmentQuestionsAnswer($id);
    }

    public function getAssessmentQuestionsAnswers()
    {
        return $this->answerRepository->getAssessmentQuestionsAnswers();
    }

    public function getAssessmentQuestionsAnswerDTO($answerId): ?AssessmentQuestionsAnswerDTO
    {
        $answer = $this->getAssessmentQuestionsAnswer($answerId);

        if (!$answer) {
            return null;
        }

        return AssessmentQuestionsAnswerDTO::fromArray($answer->toArray());
    }
}
