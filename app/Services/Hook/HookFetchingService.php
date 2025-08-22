<?php

namespace App\Services\Hook;

use App\Contracts\HookRepositoryInterface;
use App\DTOs\Hook\HookDTO;
use App\Models\Hook;

class HookFetchingService
{
    public function __construct(protected HookRepositoryInterface $hookRepository)
    {
    }

    public function getHookById($hookId)
    {
        return $this->hookRepository->getHook($hookId);
    }

    public function getAllHooks()
    {
        return $this->hookRepository->getHooks();
    }

    public function getHooksBySubjectId($subjectId)
    {
        return Hook::where('subject_id', $subjectId)->get();
    }

    public function getHooksByNegotiationId($negotiationId)
    {
        return Hook::where('negotiation_id', $negotiationId)->get();
    }

    public function getHookDTO($hookId): ?HookDTO
    {
        $hook = $this->getHookById($hookId);

        if (!$hook) {
            return null;
        }

        return HookDTO::fromArray($hook->toArray());
    }
}
