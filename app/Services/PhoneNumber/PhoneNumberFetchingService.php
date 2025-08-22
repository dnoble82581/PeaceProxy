<?php

namespace App\Services\PhoneNumber;

use App\Contracts\PhoneNumberRepositoryInterface;

class PhoneNumberFetchingService
{
    protected PhoneNumberRepositoryInterface $phoneNumberRepository;

    public function __construct(PhoneNumberRepositoryInterface $phoneNumberRepository)
    {
        $this->phoneNumberRepository = $phoneNumberRepository;
    }

    public function fetchPhoneNumberById($id)
    {
        return $this->phoneNumberRepository->getPhoneNumber($id);
    }

    public function fetchAllPhoneNumbers()
    {
        return $this->phoneNumberRepository->getPhoneNumbers();
    }
}
