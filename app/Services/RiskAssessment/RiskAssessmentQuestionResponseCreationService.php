<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionResponseRepositoryInterface;
use App\DTOs\RiskAssessment\RiskAssessmentQuestionResponseDTO;

class RiskAssessmentQuestionResponseCreationService
{
    protected RiskAssessmentQuestionResponseRepositoryInterface $responseRepository;

    public function __construct(RiskAssessmentQuestionResponseRepositoryInterface $responseRepository)
    {
        $this->responseRepository = $responseRepository;
    }

    public function createResponse(RiskAssessmentQuestionResponseDTO $responseDTO)
    {
        return $this->responseRepository->createResponse($responseDTO->toArray());
    }
}
