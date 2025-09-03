<?php

namespace App\Contracts;

interface AssessmentRepositoryInterface
{
    public function createAssessment($data);

    public function getAssessment($id);

    public function getAssessments();

    public function updateAssessment($id, $data);

    public function deleteAssessment($id);
}
