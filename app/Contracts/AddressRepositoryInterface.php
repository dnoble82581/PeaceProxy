<?php

namespace App\Contracts;

interface AddressRepositoryInterface
{
    public function createAddress($data);

    public function getAddress($id);

    public function getAddresses();

    public function updateAddress($id, $data);

    public function deleteAddress($id);
}
