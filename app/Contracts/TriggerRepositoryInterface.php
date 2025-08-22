<?php

namespace App\Contracts;

interface TriggerRepositoryInterface
{
    public function createTrigger($data);

    public function getTrigger($id);

    public function getTriggers();

    public function updateTrigger($id, $data);

    public function deleteTrigger($id);
}
