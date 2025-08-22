<?php

namespace App\Services\Address;

use App\Contracts\AddressRepositoryInterface;
use App\DTOs\Address\AddressDTO;

class AddressCreationService
{
    protected AddressRepositoryInterface $addressRepository;

    public function __construct(AddressRepositoryInterface $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function createAddress(AddressDTO $addressDTO)
    {
        return $this->addressRepository->createAddress($addressDTO->toArray());
    }
}
