<?php

namespace App\Services\NegotiationUser;

use App\DTOs\NegotiationUser\NegotiationUserDTO;
use App\Models\NegotiationUser;

class NegotiationUserCreationService
{
    public function __construct()
    {
    }

    public function createNegotiationUser(NegotiationUserDTO $negotiationUserDTO)
    {
        NegotiationUser::create($negotiationUserDTO->toArray());
    }
}
