<?php

namespace App\Models;

use App\Enums\Hook\HookCategories;
use App\Enums\Hook\HookSensitivityLevels;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hook extends Model
{
    use HasFactory;
    use BelongsToTenant;
    use SoftDeletes;

    protected $guarded = ['id'];


    protected $casts = [
        'sensitivity_level' => HookSensitivityLevels::class,
        'category' => HookCategories::class,
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
