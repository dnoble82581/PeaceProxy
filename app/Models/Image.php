<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'metadata' => 'array'
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        try {
            // If the url property is already set and not empty, use it
            if (!empty($this->url)) {
                return $this->url;
            }

            // Use the disk specified in the image record, or use the default filesystem disk
            $disk = $this->disk ?? config('filesystems.default', 'local');

            // Check if the file exists in storage
            if (Storage::disk($disk)->exists($this->path)) {
                // Generate and cache the URL
                $url = Storage::disk($disk)->url($this->path);
                $this->update(['url' => $url]);
                return $url;
            }

            // Return a default image URL if the file doesn't exist
            return asset('images/default-image.png');
        } catch (Exception $e) {
            // Log the error and return a default image URL
            Log::error('Error retrieving image URL: '.$e->getMessage());

            return 'https://placehold.co/600x400?text=Hello+World';
        }
    }
}
