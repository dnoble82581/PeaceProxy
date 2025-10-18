<?php

namespace App\Services\Image;

use App\Models\Image;
use App\Models\Tenant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LogoService
{
    public function __construct()
    {
    }

    public function set(Tenant $tenant, UploadedFile $file): Image
    {
        $disk = 's3_public';
        $extension = $file->guessExtension() ?: 'jpg';
        $path = "tenants/{$tenant->id}/images/logo_".Str::uuid().".{$extension}";

        // Remove previous avatar file and record if they exist
        if ($previous = $tenant->logo) {
            $prevDisk = $previous->disk ?: $disk;
            if ($previous->path && Storage::disk($prevDisk)->exists($previous->path)) {
                Storage::disk($prevDisk)->delete($previous->path);
            }
            $previous->delete();
        }

        // Stream upload to the target disk with appropriate metadata
        $stream = fopen($file->getRealPath(), 'rb');
        try {
            Storage::disk($disk)->put($path, $stream, [
                'visibility' => 'public',
                'CacheControl' => 'max-age=31536000, public',
                'ContentType' => $file->getMimeType(),
            ]);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        $tenant->update(['logo_path' => $path]);

        return $tenant->logo()->create([
            'tenant_id' => $tenant->id,
            'negotiation_id' => null,
            'path' => $path,
            'url' => Storage::disk($disk)->url($path),
            'type' => 'logo',
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'disk' => $disk,
            'title' => $tenant->name,
            'alt_text' => $tenant->name.' logo Image',
            'order' => $tenant->id,
        ]);
    }
}
