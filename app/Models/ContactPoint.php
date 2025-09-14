<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContactPoint extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = ['id'];

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function email(): HasOne
    {
        return $this->hasOne(ContactEmail::class);
    }

    public function phone(): HasOne
    {
        return $this->hasOne(ContactPhone::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(ContactAddress::class);
    }

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'timestamp',
        ];
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
