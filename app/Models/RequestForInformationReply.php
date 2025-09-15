<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestForInformationReply extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = ['id'];

    public function rfi()
    {
        return $this->belongsTo(RequestForInformation::class, 'request_for_information_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
