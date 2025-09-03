<?php

namespace App\Services\AssessmentQuestionsAnswer;

use App\Contracts\AssessmentQuestionsAnswerRepositoryInterface;
use App\DTOs\AssessmentQuestionsAnswer\AssessmentQuestionsAnswerDTO;

class AssessmentQuestionsAnswerUpdatingService
{
    protected AssessmentQuestionsAnswerRepositoryInterface $answerRepository;

    public function __construct(AssessmentQuestionsAnswerRepositoryInterface $answerRepository)
    {
        $this->answerRepository = $answerRepository;
    }

    public function updateAssessmentQuestionsAnswer($id, AssessmentQuestionsAnswerDTO $answerDTO)
    {
        return $this->answerRepository->updateAssessmentQuestionsAnswer($id, $answerDTO->toArray());
    }
}
