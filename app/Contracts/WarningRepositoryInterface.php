<?php

namespace App\Contracts;

interface WarningRepositoryInterface
{
    public function createWarning($data);

    public function getWarning($id);

    public function getWarnings();

    public function getWarningsBySubject($subjectId);

    public function updateWarning($id, $data);

    public function deleteWarning($id);
}
