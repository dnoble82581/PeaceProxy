<?php

namespace App\Services\NegotiationSubject;

use App\DTOs\NegotiationSubject\NegotiationSubjectDTO;
use App\Models\NegotiationSubject;

class NegotiationSubjectCreationService
{
    public function __construct()
    {
    }

    public function createNegotiationSubject(NegotiationSubjectDTO $negotiationSubjectDTO)
    {
        NegotiationSubject::create($negotiationSubjectDTO->toArray());
    }
}
