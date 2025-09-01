<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionsRepositoryInterface;
use App\DTOs\RiskAssessment\RiskAssessmentQuestionsDTO;

class RiskAssessmentQuestionsCreationService
{
    protected RiskAssessmentQuestionsRepositoryInterface $questionsRepository;

    public function __construct(RiskAssessmentQuestionsRepositoryInterface $questionsRepository)
    {
        $this->questionsRepository = $questionsRepository;
    }

    public function createQuestion(RiskAssessmentQuestionsDTO $questionDTO)
    {
        return $this->questionsRepository->createQuestion($questionDTO->toArray());
    }
}
