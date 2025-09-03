<?php

namespace App\Services\AssessmentTemplate;

use App\Contracts\AssessmentTemplateRepositoryInterface;
use App\DTOs\AssessmentTemplate\AssessmentTemplateDTO;

class AssessmentTemplateFetchingService
{
    protected AssessmentTemplateRepositoryInterface $templateRepository;

    public function __construct(AssessmentTemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    public function getAssessmentTemplate($id)
    {
        return $this->templateRepository->getAssessmentTemplate($id);
    }

    public function getAssessmentTemplates()
    {
        return $this->templateRepository->getAssessmentTemplates();
    }

    public function getAssessmentTemplateDTO($templateId): ?AssessmentTemplateDTO
    {
        $template = $this->getAssessmentTemplate($templateId);

        if (!$template) {
            return null;
        }

        return AssessmentTemplateDTO::fromArray($template->toArray());
    }
}
