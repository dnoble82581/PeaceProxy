<?php

namespace App\Contracts;

interface NegotiationSubjectRepositoryInterface
{
    public function createNegotiationSubject($data);

    public function getNegotiationSubject($id);

    public function getNegotiationSubjects();

    public function updateNegotiationSubject($id, $data);

    public function deleteNegotiationSubject($id);
}
