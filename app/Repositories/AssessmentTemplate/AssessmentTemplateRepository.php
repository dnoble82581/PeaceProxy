<?php

namespace App\Repositories\AssessmentTemplate;

use App\Contracts\AssessmentTemplateRepositoryInterface;
use App\Models\AssessmentTemplate;
use Illuminate\Database\Eloquent\Collection;

class AssessmentTemplateRepository implements AssessmentTemplateRepositoryInterface
{
    public function createAssessmentTemplate($data)
    {
        return AssessmentTemplate::create($data);
    }

    public function getAssessmentTemplates(): Collection
    {
        return AssessmentTemplate::all();
    }

    public function updateAssessmentTemplate($id, $data)
    {
        $template = $this->getAssessmentTemplate($id);
        $template->update($data);
        return $template;
    }

    public function getAssessmentTemplate($id)
    {
        return AssessmentTemplate::find($id);
    }

    public function deleteAssessmentTemplate($id)
    {
        $template = $this->getAssessmentTemplate($id);
        $template->delete();
        return $template;
    }
}
