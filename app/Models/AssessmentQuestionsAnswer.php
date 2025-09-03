<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentQuestionsAnswer extends Model
{
    use HasFactory;
    use BelongsToTenant;

    //	TODO: Make sure all assessment models are scoped by tenant. Question answers scoped by subject and negotiation and tenant. Double Check this.
    protected $guarded = ['id'];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function assessmentTemplateQuestion(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplateQuestion::class);
    }

    protected function casts(): array
    {
        return [
            'answer' => 'array',
        ];
    }
}
