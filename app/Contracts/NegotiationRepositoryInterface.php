<?php

namespace App\Contracts;

interface NegotiationRepositoryInterface
{
    public function createNegotiation($data);

    public function getNegotiation($id);

    public function getNegotiations();

    public function updateNegotiation($id, $data);

    public function deleteNegotiation($id);
}
