<?php

namespace App\Repositories\RequestForInformation;

use App\Contracts\RequestForInformationRepositoryInterface;
use App\Models\RequestForInformation;
use Illuminate\Database\Eloquent\Collection;

class RequestForInformationRepository implements RequestForInformationRepositoryInterface
{
    public function createRfi(array $data): RequestForInformation
    {
        return RequestForInformation::create($data);
    }

    public function getRfis(): Collection
    {
        return RequestForInformation::all();
    }

    public function updateRfi(int $id, array $data): ?RequestForInformation
    {
        $rfi = $this->getRfi($id);
        if ($rfi) {
            $rfi->update($data);
        }
        return $rfi;
    }

    public function getRfi(int $id): ?RequestForInformation
    {
        return RequestForInformation::find($id);
    }

    public function deleteRfi(int $id): ?RequestForInformation
    {
        $rfi = $this->getRfi($id);
        if ($rfi) {
            $rfi->delete();
        }
        return $rfi;
    }
}
