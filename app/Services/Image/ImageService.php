<?php

namespace App\Services\Image;

use App\Events\Subject\SubjectUpdatedEvent;
use App\Models\Image;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Delete an image from storage and the database.
     *
     * @param  Image  $image  The image to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteImage(Image $image): bool
    {
        try {
            // Delete the file from storage if it exists
            if (Storage::disk($image->disk)->exists($image->path)) {
                Storage::disk($image->disk)->delete($image->path);
            }

            // Delete the record from the database
            return $image->delete();
        } catch (Exception $e) {
            Log::error('Failed to delete image: '.$e->getMessage());
            return false;
        }
    }

    /**
     * Set an image as the primary image for its imageable.
     *
     * @param  Image  $image  The image to set as primary
     * @return bool Whether the operation was successful
     */
    public function setPrimaryImage(Image $image): bool
    {
        try {
            // Get the imageable type and ID
            $imageableType = $image->imageable_type;
            $imageableId = $image->imageable_id;

            // Update all images of this imageable to not be primary
            Image::where('imageable_type', $imageableType)
                ->where('imageable_id', $imageableId)
                ->update(['is_primary' => false]);

            // Set this image as primary
            return $image->update(['is_primary' => true]);
        } catch (Exception $e) {
            Log::error('Failed to set primary image: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Upload multiple images for a model.
     *
     * @param  array  $files  Array of UploadedFile objects
     * @param  object  $model  The model to attach images to
     * @param  string  $path  The base path for storing images
     * @param  string  $disk  The disk to store images on
     * @return array Array of created Image models
     */
    public function uploadImagesForModel(
        array $files,
        object $model,
        string $path,
        string $disk = 's3_public'
    ): array {
        $images = [];
        $modelType = get_class($model);
        $modelId = $model->id;
        $tenantId = $model->tenant_id ?? null;

        // Check if this model already has images
        $hasExistingImages = $model->images()->count() > 0;

        foreach ($files as $index => $file) {
            // First image is primary if no existing images
            $isPrimary = ! $hasExistingImages && $index === 0;

            $image = $this->uploadImage(
                $file,
                $path.'/'.$modelId.'/images',
                $disk,
                $tenantId,
                $isPrimary,
                $modelType,
                $modelId
            );

            if ($image) {
                $images[] = $image;
            }
        }
        //		ToDo: Add more checks for other models

        if ($model instanceof \App\Models\Subject) {
            event(new SubjectUpdatedEvent($model->id));
        }

        return $images;
    }

    /**
     * Upload an image to S3 and create an Image record.
     *
     * @param  UploadedFile  $file  The uploaded file
     * @param  string  $path  The path to store the file (e.g., 'subjects/1/images')
     * @param  string  $disk  The disk to store the file on (default: 's3_public')
     * @param  int|null  $tenantId  The tenant ID
     * @param  bool  $isPrimary  Whether this is the primary image
     * @param  string|null  $imageableType  The type of the model this image belongs to
     * @param  int|null  $imageableId  The ID of the model this image belongs to
     *
     * @return Image|null The created Image model or null if upload failed
     */
    public function uploadImage(
        UploadedFile $file,
        string $path,
        string $disk = 's3_public',
        ?int $tenantId = null,
        bool $isPrimary = false,
        ?string $imageableType = null,
        ?int $imageableId = null
    ): ?Image {
        try {
            // Store the image in the specified disk
            $filePath = $file->store($path, $disk);
            $url = Storage::disk($disk)->url($filePath);

            // Create a new Image record
            return Image::create([
                'path' => $filePath,
                'url' => $url,
                'disk' => $disk,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'tenant_id' => $tenantId,
                'is_primary' => $isPrimary,
                'imageable_type' => $imageableType,
                'imageable_id' => $imageableId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to upload image: '.$e->getMessage());

            return null;
        }
    }
}
