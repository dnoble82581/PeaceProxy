<?php

namespace App\Repositories\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionResponseRepositoryInterface;
use App\Models\AssessmentQuestionsAnswer;
use Illuminate\Database\Eloquent\Collection;

class RiskAssessmentQuestionResponseRepository implements RiskAssessmentQuestionResponseRepositoryInterface
{
    /**
     * Create a new response
     *
     * @param array $data
     * @return AssessmentQuestionsAnswer
     */
    public function createResponse(array $data)
    {
        return AssessmentQuestionsAnswer::create($data);
    }

    /**
     * Get a response by ID
     *
     * @param int $id
     * @return AssessmentQuestionsAnswer|null
     */
    public function getResponse(int $id)
    {
        return AssessmentQuestionsAnswer::find($id);
    }

    /**
     * Get all responses
     *
     * @return Collection
     */
    public function getResponses(): Collection
    {
        return AssessmentQuestionsAnswer::all();
    }

    /**
     * Get responses by question ID
     *
     * @param int $questionId
     * @return Collection
     */
    public function getResponsesByQuestion(int $questionId): Collection
    {
        return AssessmentQuestionsAnswer::where('assessment_template_question_id', $questionId)->get();
    }

    /**
     * Get responses by user ID
     *
     * @param int $userId
     * @return Collection
     */
    public function getResponsesByUser(int $userId): Collection
    {
        return AssessmentQuestionsAnswer::where('created_by_id', $userId)->get();
    }

    /**
     * Get responses by question ID and user ID
     *
     * @param int $questionId
     * @param int $userId
     * @return Collection
     */
    public function getResponsesByQuestionAndUser(int $questionId, int $userId): Collection
    {
        return AssessmentQuestionsAnswer::where('assessment_template_question_id', $questionId)
            ->where('created_by_id', $userId)
            ->get();
    }

    /**
     * Update a response
     *
     * @param int $id
     * @param array $data
     * @return AssessmentQuestionsAnswer|null
     */
    public function updateResponse(int $id, array $data)
    {
        $response = $this->getResponse($id);
        if ($response) {
            $response->update($data);
        }
        return $response;
    }

    /**
     * Delete a response
     *
     * @param int $id
     * @return AssessmentQuestionsAnswer|null
     */
    public function deleteResponse(int $id)
    {
        $response = $this->getResponse($id);
        if ($response) {
            $response->delete();
        }
        return $response;
    }
}
