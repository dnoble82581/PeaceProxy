<?php

namespace App\Repositories\Warning;

use App\Contracts\WarningRepositoryInterface;
use App\Models\Warning;
use Illuminate\Database\Eloquent\Collection;

class WarningRepository implements WarningRepositoryInterface
{
    public function createWarning($data)
    {
        return Warning::create($data);
    }

    public function getWarnings(): Collection
    {
        return Warning::all();
    }

    public function updateWarning($id, $data)
    {
        $warning = $this->getWarning($id);
        $warning->update($data);
        return $warning;
    }

    public function getWarning($id)
    {
        return Warning::findOrFail($id);
    }

    public function deleteWarning($id)
    {
        return Warning::destroy($id);
    }

    public function getWarningsBySubject($subjectId): Collection
    {
        return Warning::where('subject_id', $subjectId)->get();
    }
}
