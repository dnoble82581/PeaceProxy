<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionResponseRepositoryInterface;

class RiskAssessmentQuestionResponseDestructionService
{
    protected RiskAssessmentQuestionResponseRepositoryInterface $responseRepository;

    public function __construct(RiskAssessmentQuestionResponseRepositoryInterface $responseRepository)
    {
        $this->responseRepository = $responseRepository;
    }

    public function deleteResponse(int $id)
    {
        return $this->responseRepository->deleteResponse($id);
    }
}
