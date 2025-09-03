<?php

namespace App\Http\Resources;

use App\Models\AssessmentTemplateQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AssessmentTemplateQuestion */
class AssessmentTemplateQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'question_type' => $this->question_type,
            'question_category' => $this->question_category,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
