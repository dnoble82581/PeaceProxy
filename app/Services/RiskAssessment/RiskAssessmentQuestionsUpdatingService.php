<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionsRepositoryInterface;
use App\DTOs\RiskAssessment\RiskAssessmentQuestionsDTO;

class RiskAssessmentQuestionsUpdatingService
{
    protected RiskAssessmentQuestionsRepositoryInterface $questionsRepository;

    public function __construct(RiskAssessmentQuestionsRepositoryInterface $questionsRepository)
    {
        $this->questionsRepository = $questionsRepository;
    }

    public function updateQuestion(int $id, RiskAssessmentQuestionsDTO $questionDTO)
    {
        return $this->questionsRepository->updateQuestion($id, $questionDTO->toArray());
    }
}
