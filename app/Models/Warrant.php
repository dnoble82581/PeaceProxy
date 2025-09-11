<?php

namespace App\Models;

use App\Enums\Warrant\BondType;
use App\Enums\Warrant\WarrantStatus;
use App\Enums\Warrant\WarrantType;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

class Warrant extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => WarrantStatus::class,
        'bond_type' => BondType::class,
        'type' => WarrantType::class,
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function bondAmount()
    {
        if ($this->bond_amount) {
            return Number::currency($this->bond_amount);
        }

        return 0;
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
