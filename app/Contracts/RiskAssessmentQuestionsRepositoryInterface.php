<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface RiskAssessmentQuestionsRepositoryInterface
{
    public function createQuestion(array $data);

    public function getQuestion(int $id);

    public function getQuestions(): Collection;

    public function getQuestionsByNegotiation(int $negotiationId): Collection;

    public function getActiveQuestions(): Collection;

    public function getActiveQuestionsByNegotiation(int $negotiationId): Collection;

    public function getQuestionsByCategory(string $category): Collection;

    public function updateQuestion(int $id, array $data);

    public function deleteQuestion(int $id);
}
