<?php

namespace App\Contracts;

interface PhoneNumberRepositoryInterface
{
    public function createPhoneNumber($data);

    public function getPhoneNumber($id);

    public function getPhoneNumbers();

    public function updatePhoneNumber($id, $data);

    public function deletePhoneNumber($id);
}
