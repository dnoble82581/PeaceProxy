<?php

namespace App\Repositories\Address;

use App\Contracts\AddressRepositoryInterface;
use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;

class AddressRepository implements AddressRepositoryInterface
{
    public function createAddress($data)
    {
        return Address::create($data);
    }

    public function getAddresses(): Collection
    {
        return Address::all();
    }

    public function updateAddress($id, $data)
    {
        $address = $this->getAddress($id);
        $address->update($data);
        return $address;
    }

    public function getAddress($id)
    {
        return Address::find($id);
    }

    public function deleteAddress($id)
    {
        $address = $this->getAddress($id);
        $address->delete();
        return $address;
    }
}
