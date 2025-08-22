<?php

namespace App\Services\Contact;

use App\Contracts\ContactRepositoryInterface;

class ContactDestructionService
{
    public function __construct(protected ContactRepositoryInterface $contactRepository)
    {
    }

    public function deleteContact($contactId): void
    {
        $this->contactRepository->deleteContact($contactId);
    }
}
