<?php

namespace App\Contracts;

interface HookRepositoryInterface
{
    public function createHook($data);

    public function getHook($id);

    public function getHooks();

    public function updateHook($id, $data);

    public function deleteHook($id);
}
