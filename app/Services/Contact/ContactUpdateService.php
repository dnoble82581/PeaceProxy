<?php

namespace App\Services\Contact;

use App\Contracts\ContactRepositoryInterface;
use App\DTOs\Contact\ContactDTO;

class ContactUpdateService
{
    protected ContactRepositoryInterface $contactRepository;

    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function updateContact(int $id, ContactDTO $contactDTO)
    {
        return $this->contactRepository->updateContact($id, $contactDTO->toArray());
    }
}
