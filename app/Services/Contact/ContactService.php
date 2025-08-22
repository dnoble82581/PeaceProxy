<?php

namespace App\Services\Contact;

use App\Contracts\ContactRepositoryInterface;

class ContactService
{
    public function __construct(protected ContactRepositoryInterface $contactRepository)
    {
    }

    public function createContact($data)
    {
        return $this->contactRepository->createContact($data);
    }

    public function deleteContact($contactId): void
    {
        $this->contactRepository->deleteContact($contactId);
    }

    public function getContactById($contactId)
    {
        return $this->contactRepository->getContact($contactId);
    }
}
