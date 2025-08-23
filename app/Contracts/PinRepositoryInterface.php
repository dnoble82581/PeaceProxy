<?php

namespace App\Contracts;

interface PinRepositoryInterface
{
    public function createPin($data);

    public function getPin($id);

    public function getPinByPinnable($pinnableType, $pinnableId);

    public function getPins();

    public function deletePin($id);

    public function deletePinByPinnable($pinnableType, $pinnableId);
}
