<?php

namespace App\Contracts;

interface WarrantRepositoryInterface
{
    public function createWarrant($data);

    public function getWarrant($id);

    public function getWarrants();

    public function updateWarrant($data, $id);

    public function deleteWarrant($id);
}
