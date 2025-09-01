<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionResponseRepositoryInterface;
use App\DTOs\RiskAssessment\RiskAssessmentQuestionResponseDTO;

class RiskAssessmentQuestionResponseUpdatingService
{
    protected RiskAssessmentQuestionResponseRepositoryInterface $responseRepository;

    public function __construct(RiskAssessmentQuestionResponseRepositoryInterface $responseRepository)
    {
        $this->responseRepository = $responseRepository;
    }

    public function updateResponse(int $id, RiskAssessmentQuestionResponseDTO $responseDTO)
    {
        return $this->responseRepository->updateResponse($id, $responseDTO->toArray());
    }
}
