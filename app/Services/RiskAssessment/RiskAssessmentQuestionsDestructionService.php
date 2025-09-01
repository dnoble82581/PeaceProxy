<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionsRepositoryInterface;

class RiskAssessmentQuestionsDestructionService
{
    protected RiskAssessmentQuestionsRepositoryInterface $questionsRepository;

    public function __construct(RiskAssessmentQuestionsRepositoryInterface $questionsRepository)
    {
        $this->questionsRepository = $questionsRepository;
    }

    public function deleteQuestion(int $id)
    {
        return $this->questionsRepository->deleteQuestion($id);
    }
}
