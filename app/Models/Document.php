<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasFactory;
    use BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'negotiation_id',
        'tenant_id',
        'name',
        'file_path',
        'file_type',
        'file_size',
        'storage_disk',
        'documentable_id',
        'documentable_type',
        'category',
        'description',
        'is_private',
        'tags',
        'uploaded_by_id',
        'encrypted',
        'access_token',
        'presigned_url_expires_at',
    ];

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
