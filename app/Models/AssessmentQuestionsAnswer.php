<?php

namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    
    class AssessmentQuestionsAnswer extends Model {
        use HasFactory;
        
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
