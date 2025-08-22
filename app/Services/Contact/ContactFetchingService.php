<?php

namespace App\Services\Contact;

use App\Contracts\ContactRepositoryInterface;

class ContactFetchingService
{
    protected ContactRepositoryInterface $contactRepository;

    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function fetchContactById($id)
    {
        return $this->contactRepository->getContact($id);
    }

    public function fetchAllContacts()
    {
        return $this->contactRepository->getContacts();
    }
}
