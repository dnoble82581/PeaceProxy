<?php

namespace App\Http\Resources;

use App\Models\AssessmentQuestionsAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AssessmentQuestionsAnswer */
class AssessmentQuestionsAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'answer' => $this->answer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'assessment_id' => $this->assessment_id,
            'assessment_template_question_id' => $this->assessment_template_question_id,

            'assessment' => new AssessmentResource($this->whenLoaded('assessment')),
            'assessmentTemplateQuestion' => new AssessmentTemplateQuestionResource($this->whenLoaded('assessmentTemplateQuestion')),

        ];
    }
}
