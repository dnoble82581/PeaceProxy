<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPhone extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function contactPoint(): BelongsTo
    {
        return $this->belongsTo(ContactPoint::class);
    }
}
