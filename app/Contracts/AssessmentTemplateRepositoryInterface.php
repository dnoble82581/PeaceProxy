<?php

namespace App\Contracts;

interface AssessmentTemplateRepositoryInterface
{
    public function createAssessmentTemplate($data);

    public function getAssessmentTemplate($id);

    public function getAssessmentTemplates();

    public function updateAssessmentTemplate($id, $data);

    public function deleteAssessmentTemplate($id);
}
