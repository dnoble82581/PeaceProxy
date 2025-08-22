<?php

namespace App\Repositories\PhoneNumber;

use App\Contracts\PhoneNumberRepositoryInterface;
use App\Models\PhoneNumber;
use Illuminate\Database\Eloquent\Collection;

class PhoneNumberRepository implements PhoneNumberRepositoryInterface
{
    public function createPhoneNumber($data)
    {
        return PhoneNumber::create($data);
    }

    public function getPhoneNumbers(): Collection
    {
        return PhoneNumber::all();
    }

    public function updatePhoneNumber($id, $data)
    {
        $phoneNumber = $this->getPhoneNumber($id);
        $phoneNumber->update($data);
        return $phoneNumber;
    }

    public function getPhoneNumber($id)
    {
        return PhoneNumber::find($id);
    }

    public function deletePhoneNumber($id)
    {
        $phoneNumber = $this->getPhoneNumber($id);
        $phoneNumber->delete();
        return $phoneNumber;
    }
}
