<?php

namespace App\Repositories\Hook;

use App\Contracts\HookRepositoryInterface;
use App\Models\Hook;
use Illuminate\Database\Eloquent\Collection;

class HookRepository implements HookRepositoryInterface
{
    public function createHook($data)
    {
        return Hook::create($data);
    }

    public function getHooks(): Collection
    {
        return Hook::all();
    }

    public function updateHook($id, $data)
    {
        $hook = $this->getHook($id);
        $hook->update($data);
        return $hook;
    }

    public function getHook($id)
    {
        return Hook::find($id);
    }

    public function deleteHook($id)
    {
        $hook = $this->getHook($id);
        $hook->delete();
        return $hook;
    }
}
