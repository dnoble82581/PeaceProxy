<?php

namespace App\Contracts;

interface SubjectRepositoryInterface
{
    public function createSubject($data);

    public function getSubject($id);

    public function getSubjects();

    public function updateSubject($id, $data);

    public function deleteSubject($id);
}
