<?php

namespace App\Repositories\Warrant;

use App\Contracts\WarrantRepositoryInterface;
use App\Models\Warrant;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_Warrant_C;

class WarrantRepository implements WarrantRepositoryInterface
{
    public function createWarrant($data)
    {
        if (!isset($data['created_by']) || empty($data['created_by'])) {
            $data['created_by'] = auth()->id();
        }
        return Warrant::create($data);
    }

    public function getWarrants(): Collection|_IH_Warrant_C|array
    {
        return Warrant::all();
    }

    public function updateWarrant($data, $id)
    {
        $warrant = $this->getWarrant($id);
        $warrant->update($data);
        return $warrant;
    }

    public function getWarrant($id)
    {
        return Warrant::find($id);
    }

    public function deleteWarrant($id)
    {
        $warrant = $this->getWarrant($id);
        $warrant->delete();
        return $warrant;
    }
}
