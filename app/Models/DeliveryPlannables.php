<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryPlannables extends Model
{
    use HasFactory;

    public function deliveryPlan(): BelongsTo
    {
        return $this->belongsTo(DeliveryPlan::class);
    }
}
