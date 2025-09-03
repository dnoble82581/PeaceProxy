<?php

namespace App\Services\AssessmentTemplate;

use App\Contracts\AssessmentTemplateRepositoryInterface;

class AssessmentTemplateDestructionService
{
    protected AssessmentTemplateRepositoryInterface $templateRepository;

    public function __construct(AssessmentTemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    public function deleteAssessmentTemplate($id)
    {
        return $this->templateRepository->deleteAssessmentTemplate($id);
    }
}
