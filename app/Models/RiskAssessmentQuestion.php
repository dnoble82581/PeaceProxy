<?php

namespace App\Models;

use App\Enums\Assessment\QuestionCategories;
use App\Enums\Assessment\QuestionResponseTypes;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RiskAssessmentQuestion extends Model
{
    use BelongsToTenant;

    protected $guarded = [
        'id',
    ];

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class, 'negotiation_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function riskAssessment(): BelongsTo
    {
        return $this->belongsTo(RiskAssessment::class, 'risk_assessment_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(RiskAssessmentQuestionResponse::class, 'question_id');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'type' => QuestionResponseTypes::class,
            'category' => QuestionCategories::class,
        ];
    }
}
