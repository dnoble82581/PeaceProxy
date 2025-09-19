<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use BelongsToTenant;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'is_private' => 'boolean',
        'encrypted' => 'boolean',
        'tags' => 'array',
        'presigned_url_expires_at' => 'datetime',
    ];

    public function deliveryPlans()
    {
        return $this->morphedByMany(DeliveryPlan::class, 'documentable')->withTimestamps();
    }

    /**
     * Get the negotiation that owns the document.
     */
    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    /**
     * Get the parent documentable model (subject, user, negotiation).
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the logs for this document.
     */
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
