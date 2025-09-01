<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface RiskAssessmentQuestionResponseRepositoryInterface
{
    public function createResponse(array $data);

    public function getResponse(int $id);

    public function getResponses(): Collection;

    public function getResponsesByQuestion(int $questionId): Collection;

    public function getResponsesByUser(int $userId): Collection;

    public function getResponsesByQuestionAndUser(int $questionId, int $userId): Collection;

    public function updateResponse(int $id, array $data);

    public function deleteResponse(int $id);
}
