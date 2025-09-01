<?php

namespace App\Repositories\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionResponseRepositoryInterface;
use App\Models\RiskAssessmentQuestionResponse;
use Illuminate\Database\Eloquent\Collection;

class RiskAssessmentQuestionResponseRepository implements RiskAssessmentQuestionResponseRepositoryInterface
{
    public function createResponse(array $data)
    {
        return RiskAssessmentQuestionResponse::create($data);
    }

    public function getResponse(int $id)
    {
        return RiskAssessmentQuestionResponse::findOrFail($id);
    }

    public function getResponses(): Collection
    {
        return RiskAssessmentQuestionResponse::all();
    }

    public function getResponsesByQuestion(int $questionId): Collection
    {
        return RiskAssessmentQuestionResponse::where('question_id', $questionId)->get();
    }

    public function getResponsesByUser(int $userId): Collection
    {
        return RiskAssessmentQuestionResponse::where('created_by_id', $userId)->get();
    }

    public function getResponsesByQuestionAndUser(int $questionId, int $userId): Collection
    {
        return RiskAssessmentQuestionResponse::where('question_id', $questionId)
            ->where('created_by_id', $userId)
            ->get();
    }

    public function updateResponse(int $id, array $data)
    {
        $response = $this->getResponse($id);
        $response->update($data);
        return $response;
    }

    public function deleteResponse(int $id)
    {
        return RiskAssessmentQuestionResponse::destroy($id);
    }
}
