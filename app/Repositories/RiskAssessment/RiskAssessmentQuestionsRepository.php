<?php

namespace App\Repositories\RiskAssessment;

use App\Contracts\RiskAssessmentQuestionsRepositoryInterface;
use App\Models\RiskAssessmentQuestion;
use Illuminate\Database\Eloquent\Collection;

class RiskAssessmentQuestionsRepository implements RiskAssessmentQuestionsRepositoryInterface
{
    public function createQuestion(array $data)
    {
        return RiskAssessmentQuestion::create($data);
    }

    public function getQuestions(): Collection
    {
        return RiskAssessmentQuestion::all();
    }

    public function getQuestionsByNegotiation(int $negotiationId): Collection
    {
        return RiskAssessmentQuestion::where('negotiation_id', $negotiationId)->get();
    }

    public function getActiveQuestions(): Collection
    {
        return RiskAssessmentQuestion::where('is_active', true)->get();
    }

    public function getActiveQuestionsByNegotiation(int $negotiationId): Collection
    {
        return RiskAssessmentQuestion::where('negotiation_id', $negotiationId)
            ->where('is_active', true)
            ->get();
    }

    public function getQuestionsByCategory(string $category): Collection
    {
        return RiskAssessmentQuestion::where('category', $category)->get();
    }

    public function updateQuestion(int $id, array $data)
    {
        $question = $this->getQuestion($id);
        $question->update($data);
        return $question;
    }

    public function getQuestion(int $id)
    {
        return RiskAssessmentQuestion::findOrFail($id);
    }

    public function deleteQuestion(int $id)
    {
        return RiskAssessmentQuestion::destroy($id);
    }
}
