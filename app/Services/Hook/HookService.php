<?php

namespace App\Services\Hook;

use App\Contracts\HookRepositoryInterface;

class HookService
{
    public function __construct(protected HookRepositoryInterface $hookRepository)
    {
    }

    public function createHook($data)
    {
        return $this->hookRepository->createHook($data);
    }

    public function deleteHook($hookId): void
    {
        $this->hookRepository->deleteHook($hookId);
    }

    public function getHookById($hookId)
    {
        return $this->hookRepository->getHook($hookId);
    }

    public function getAllHooks()
    {
        return $this->hookRepository->getHooks();
    }

    public function updateHook($hookId, $data)
    {
        return $this->hookRepository->updateHook($hookId, $data);
    }
}
