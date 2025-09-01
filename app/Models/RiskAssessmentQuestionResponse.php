<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskAssessmentQuestionResponse extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = ['id'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(RiskAssessmentQuestion::class, 'question_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    protected function casts(): array
    {
        return [
            'multiselect_response' => 'array',
            'checkbox_response' => 'array',
            'date_response' => 'date',
            'time_response' => 'datetime',
            'datetime_response' => 'datetime',
            'number_response' => 'integer',
        ];
    }
}
