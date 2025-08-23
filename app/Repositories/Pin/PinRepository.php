<?php

namespace App\Repositories\Pin;

use App\Contracts\PinRepositoryInterface;
use App\Models\Pin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PinRepository implements PinRepositoryInterface
{
    public function createPin($data)
    {
        return Pin::create($data);
    }

    public function getPin($id)
    {
        return Pin::find($id);
    }

    public function getPinByPinnable($pinnableType, $pinnableId)
    {
        return Pin::where('pinnable_type', $pinnableType)
            ->where('pinnable_id', $pinnableId)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->first();
    }

    public function getPins(): Collection
    {
        return Pin::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function deletePin($id)
    {
        $pin = $this->getPin($id);
        if ($pin) {
            $pin->delete();
        }
        return $pin;
    }

    public function deletePinByPinnable($pinnableType, $pinnableId)
    {
        $pin = $this->getPinByPinnable($pinnableType, $pinnableId);
        if ($pin) {
            $pin->delete();
        }
        return $pin;
    }
}
