<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RiskAssessmentQuestionsFetchingService
{
    protected RiskAssessmentQuestionsRepositoryInterface $questionsRepository;

    public function __construct(RiskAssessmentQuestionsRepositoryInterface $questionsRepository)
    {
        $this->questionsRepository = $questionsRepository;
    }

    public function getQuestion(int $id)
    {
        return $this->questionsRepository->getQuestion($id);
    }

    public function getQuestions(): Collection
    {
        return $this->questionsRepository->getQuestions();
    }

    public function getQuestionsByNegotiation(int $negotiationId): Collection
    {
        return $this->questionsRepository->getQuestionsByNegotiation($negotiationId);
    }

    public function getActiveQuestions(): Collection
    {
        return $this->questionsRepository->getActiveQuestions();
    }

    public function getActiveQuestionsByNegotiation(int $negotiationId): Collection
    {
        return $this->questionsRepository->getActiveQuestionsByNegotiation($negotiationId);
    }

    public function getQuestionsByCategory(string $category): Collection
    {
        return $this->questionsRepository->getQuestionsByCategory($category);
    }
}
