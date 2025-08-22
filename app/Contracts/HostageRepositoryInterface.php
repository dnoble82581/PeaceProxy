<?php

namespace App\Contracts;

interface HostageRepositoryInterface
{
    public function createHostage($data);

    public function getHostage($id);

    public function getHostages();

    public function getHostagesByNegotiation($negotiationId);

    public function updateHostage($id, $data);

    public function deleteHostage($id);
}
