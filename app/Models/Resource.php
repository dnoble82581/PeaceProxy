<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
