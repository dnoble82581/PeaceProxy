<?php

namespace App\Contracts;

interface NegotiationUserRepositoryInterface
{
    public function createNegotiationUser($data);

    public function getNegotiationUser($id);

    public function getNegotiationUsers();

    public function updateNegotiationUser($id, $data);

    public function deleteNegotiationUser($id);
}
