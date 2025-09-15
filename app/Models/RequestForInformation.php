<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestForInformation extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = ['id'];

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recipients()
    {
        return $this->hasMany(RequestForInformationRecipient::class);
    }

    public function replies()
    {
        return $this->hasMany(RequestForInformationReply::class);
    }
}
