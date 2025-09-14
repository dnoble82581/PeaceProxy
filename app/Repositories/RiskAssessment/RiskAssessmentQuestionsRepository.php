<?php

namespace App\Repositories\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionsRepositoryInterface;
use App\Models\AssessmentTemplateQuestion;
use Illuminate\Database\Eloquent\Collection;

class RiskAssessmentQuestionsRepository implements RiskAssessmentQuestionsRepositoryInterface
{
    /**
     * Create a new question
     *
     * @param array $data
     * @return AssessmentTemplateQuestion
     */
    public function createQuestion(array $data)
    {
        return AssessmentTemplateQuestion::create($data);
    }

    /**
     * Get a question by ID
     *
     * @param int $id
     * @return AssessmentTemplateQuestion|null
     */
    public function getQuestion(int $id)
    {
        return AssessmentTemplateQuestion::find($id);
    }

    /**
     * Get all questions
     *
     * @return Collection
     */
    public function getQuestions(): Collection
    {
        return AssessmentTemplateQuestion::all();
    }

    /**
     * Get questions by negotiation ID
     *
     * @param int $negotiationId
     * @return Collection
     */
    public function getQuestionsByNegotiation(int $negotiationId): Collection
    {
        return AssessmentTemplateQuestion::where('negotiation_id', $negotiationId)->get();
    }

    /**
     * Get active questions
     *
     * @return Collection
     */
    public function getActiveQuestions(): Collection
    {
        return AssessmentTemplateQuestion::where('is_active', true)->get();
    }

    /**
     * Get active questions by negotiation ID
     *
     * @param int $negotiationId
     * @return Collection
     */
    public function getActiveQuestionsByNegotiation(int $negotiationId): Collection
    {
        return AssessmentTemplateQuestion::where('negotiation_id', $negotiationId)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get questions by category
     *
     * @param string $category
     * @return Collection
     */
    public function getQuestionsByCategory(string $category): Collection
    {
        return AssessmentTemplateQuestion::where('category', $category)->get();
    }

    /**
     * Update a question
     *
     * @param int $id
     * @param array $data
     * @return AssessmentTemplateQuestion|null
     */
    public function updateQuestion(int $id, array $data)
    {
        $question = $this->getQuestion($id);
        if ($question) {
            $question->update($data);
        }
        return $question;
    }

    /**
     * Delete a question
     *
     * @param int $id
     * @return AssessmentTemplateQuestion|null
     */
    public function deleteQuestion(int $id)
    {
        $question = $this->getQuestion($id);
        if ($question) {
            $question->delete();
        }
        return $question;
    }
}
