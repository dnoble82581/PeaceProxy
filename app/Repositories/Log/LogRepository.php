<?php

namespace App\Repositories\Log;

use App\DTOs\Log\CreateLogDTO;
use App\Models\Log;

class LogRepository
{
    public function create(CreateLogDTO $data): Log
    {
        return Log::create($data->toArray());
    }
}
