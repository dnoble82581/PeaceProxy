<?php

namespace App\Contracts;

use App\Models\RequestForInformation;
use Illuminate\Database\Eloquent\Collection;

interface RequestForInformationRepositoryInterface
{
    public function createRfi(array $data): RequestForInformation;

    public function getRfi(int $id): ?RequestForInformation;

    public function getRfis(): Collection;

    public function updateRfi(int $id, array $data): ?RequestForInformation;

    public function deleteRfi(int $id): ?RequestForInformation;
}
