<?php

namespace App\Services\Contact;

use App\Contracts\ContactRepositoryInterface;

class ContactDeletionService
{
    protected ContactRepositoryInterface $contactRepository;

    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function deleteContact(int $id)
    {
        return $this->contactRepository->deleteContact($id);
    }
}
