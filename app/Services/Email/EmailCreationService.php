<?php

namespace App\Services\Email;

use App\Contracts\EmailRepositoryInterface;
use App\DTOs\Email\EmailDTO;

class EmailCreationService
{
    protected EmailRepositoryInterface $emailRepository;

    public function __construct(EmailRepositoryInterface $emailRepository)
    {
        $this->emailRepository = $emailRepository;
    }

    public function createEmail(EmailDTO $emailDTO)
    {
        return $this->emailRepository->createEmail($emailDTO->toArray());
    }
}
