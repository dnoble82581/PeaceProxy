<?php

namespace App\Services\Email;

use App\Contracts\EmailRepositoryInterface;

class EmailFetchingService
{
    protected EmailRepositoryInterface $emailRepository;

    public function __construct(EmailRepositoryInterface $emailRepository)
    {
        $this->emailRepository = $emailRepository;
    }

    public function fetchEmailById($id)
    {
        return $this->emailRepository->getEmail($id);
    }

    public function fetchAllEmails()
    {
        return $this->emailRepository->getEmails();
    }
}
