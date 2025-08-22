<?php

namespace App\Services\Address;

use App\Contracts\AddressRepositoryInterface;

class AddressFetchingService
{
    protected AddressRepositoryInterface $addressRepository;

    public function __construct(AddressRepositoryInterface $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function fetchAddressById($id)
    {
        return $this->addressRepository->getAddress($id);
    }

    public function fetchAllAddresses()
    {
        return $this->addressRepository->getAddresses();
    }
}
