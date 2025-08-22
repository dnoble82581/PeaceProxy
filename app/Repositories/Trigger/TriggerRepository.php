<?php

namespace App\Repositories\Trigger;

use App\Contracts\TriggerRepositoryInterface;
use App\Models\Trigger;
use Illuminate\Database\Eloquent\Collection;

class TriggerRepository implements TriggerRepositoryInterface
{
    public function createTrigger($data)
    {
        return Trigger::create($data);
    }

    public function getTriggers(): Collection
    {
        return Trigger::all();
    }

    public function updateTrigger($id, $data)
    {
        $trigger = $this->getTrigger($id);
        $trigger->update($data);
        return $trigger;
    }

    public function getTrigger($id)
    {
        return Trigger::find($id);
    }

    public function deleteTrigger($id)
    {
        $trigger = $this->getTrigger($id);
        $trigger->delete();
        return $trigger;
    }
}
