<?php

namespace App\Repositories\Contact;

use App\Contracts\ContactRepositoryInterface;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;

class ContactRepository implements ContactRepositoryInterface
{
    public function createContact($data)
    {
        return Contact::create($data);
    }

    public function getContacts(): Collection
    {
        return Contact::all();
    }

    public function updateContact($id, $data)
    {
        $contact = $this->getContact($id);
        $contact->update($data);
        return $contact;
    }

    public function getContact($id)
    {
        return Contact::find($id);
    }

    public function deleteContact($id)
    {
        $contact = $this->getContact($id);
        $contact->delete();
        return $contact;
    }
}
