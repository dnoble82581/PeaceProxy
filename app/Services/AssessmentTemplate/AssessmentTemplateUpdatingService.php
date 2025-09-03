<?php

namespace App\Services\AssessmentTemplate;

use App\Contracts\AssessmentTemplateRepositoryInterface;
use App\DTOs\AssessmentTemplate\AssessmentTemplateDTO;

class AssessmentTemplateUpdatingService
{
    protected AssessmentTemplateRepositoryInterface $templateRepository;

    public function __construct(AssessmentTemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    public function updateAssessmentTemplate($id, AssessmentTemplateDTO $templateDTO)
    {
        return $this->templateRepository->updateAssessmentTemplate($id, $templateDTO->toArray());
    }
}
