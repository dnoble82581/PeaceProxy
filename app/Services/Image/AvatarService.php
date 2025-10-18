<?php

namespace App\Services\Image;

use App\Models\Image;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    /**
     * Store and set a user's avatar image, replacing any existing one.
     *
     * - Streams file to storage to reduce memory usage.
     * - Deletes the previous avatar file and record if present.
     * - Updates the user's avatar_path to the new stored path.
     */
    public function set(User $user, UploadedFile $file): Image
    {
        $disk = 's3_public';
        $extension = $file->guessExtension() ?: 'jpg';
        $path = "tenants/{$user->tenant_id}/users/{$user->id}/avatar_".Str::uuid().".{$extension}";

        // Remove previous avatar file and record if they exist
        if ($previous = $user->avatar) {
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

        $user->update(['avatar_path' => $path]);

        return $user->avatar()->create([
            'tenant_id' => $user->tenant_id,
            'negotiation_id' => null,
            'path' => $path,
            'url' => Storage::disk($disk)->url($path),
            'type' => 'avatar',
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'disk' => $disk,
            'title' => $user->name,
            'alt_text' => $user->name.' avatar Image',
            'order' => $user->id,
        ]);
    }
}
