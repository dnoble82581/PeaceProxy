<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionResponseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RiskAssessmentQuestionResponseFetchingService
{
    protected RiskAssessmentQuestionResponseRepositoryInterface $responseRepository;

    public function __construct(RiskAssessmentQuestionResponseRepositoryInterface $responseRepository)
    {
        $this->responseRepository = $responseRepository;
    }

    public function getResponse(int $id)
    {
        return $this->responseRepository->getResponse($id);
    }

    public function getResponses(): Collection
    {
        return $this->responseRepository->getResponses();
    }

    public function getResponsesByQuestion(int $questionId): Collection
    {
        return $this->responseRepository->getResponsesByQuestion($questionId);
    }

    public function getResponsesByUser(int $userId): Collection
    {
        return $this->responseRepository->getResponsesByUser($userId);
    }

    public function getResponsesByQuestionAndUser(int $questionId, int $userId): Collection
    {
        return $this->responseRepository->getResponsesByQuestionAndUser($questionId, $userId);
    }
}
