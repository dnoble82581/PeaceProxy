<?php

namespace App\Services\AssessmentTemplate;

use App\Contracts\AssessmentTemplateRepositoryInterface;
use App\DTOs\AssessmentTemplate\AssessmentTemplateDTO;

class AssessmentTemplateCreationService
{
    protected AssessmentTemplateRepositoryInterface $templateRepository;

    public function __construct(AssessmentTemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    public function createAssessmentTemplate(AssessmentTemplateDTO $templateDTO)
    {
        return $this->templateRepository->createAssessmentTemplate($templateDTO->toArray());
    }
}
