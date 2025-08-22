<?php

namespace App\Contracts;

interface ContactRepositoryInterface
{
    public function createContact($data);

    public function getContact($id);

    public function getContacts();

    public function updateContact($id, $data);

    public function deleteContact($id);
}
