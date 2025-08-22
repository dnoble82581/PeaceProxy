<?php

namespace App\Services\PhoneNumber;

use App\Contracts\PhoneNumberRepositoryInterface;
use App\DTOs\PhoneNumber\PhoneNumberDTO;

class PhoneNumberCreationService
{
    protected PhoneNumberRepositoryInterface $phoneNumberRepository;

    public function __construct(PhoneNumberRepositoryInterface $phoneNumberRepository)
    {
        $this->phoneNumberRepository = $phoneNumberRepository;
    }

    public function createPhoneNumber(PhoneNumberDTO $phoneNumberDTO)
    {
        return $this->phoneNumberRepository->createPhoneNumber($phoneNumberDTO->toArray());
    }
}
