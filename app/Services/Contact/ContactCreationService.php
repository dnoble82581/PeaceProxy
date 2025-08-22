<?php

namespace App\Services\Contact;

use App\Contracts\ContactRepositoryInterface;
use App\DTOs\Contact\ContactDTO;

class ContactCreationService
{
    protected ContactRepositoryInterface $contactRepository;

    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function createContact(ContactDTO $contactDTO)
    {
        return $this->contactRepository->createContact($contactDTO->toArray());
    }
}
