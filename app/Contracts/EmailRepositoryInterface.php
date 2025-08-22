<?php

namespace App\Contracts;

interface EmailRepositoryInterface
{
    public function createEmail($data);

    public function getEmail($id);

    public function getEmails();

    public function updateEmail($id, $data);

    public function deleteEmail($id);
}
