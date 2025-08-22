<?php

namespace App\Repositories\Email;

use App\Contracts\EmailRepositoryInterface;
use App\Models\Email;
use Illuminate\Database\Eloquent\Collection;

class EmailRepository implements EmailRepositoryInterface
{
    public function createEmail($data)
    {
        return Email::create($data);
    }

    public function getEmails(): Collection
    {
        return Email::all();
    }

    public function updateEmail($id, $data)
    {
        $email = $this->getEmail($id);
        $email->update($data);
        return $email;
    }

    public function getEmail($id)
    {
        return Email::find($id);
    }

    public function deleteEmail($id)
    {
        $email = $this->getEmail($id);
        $email->delete();
        return $email;
    }
}
