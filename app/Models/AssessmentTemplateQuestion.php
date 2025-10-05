<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentTemplateQuestion extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function assessmentTemplate(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplate::class);
    }
}
