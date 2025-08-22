<?php

namespace App\Repositories\Document;

use App\Contracts\DocumentRepositoryInterface;
use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentRepository implements DocumentRepositoryInterface
{
    /**
     * Create a new document
     *
     * @param array $data Document data
     * @param UploadedFile|null $file The uploaded file
     * @return Document
     */
    public function createDocument(array $data, ?UploadedFile $file = null)
    {
        if ($file) {
            // Generate a unique file name
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Get the documentable type and ID for the folder structure
            $documentableType = Str::lower(class_basename($data['documentable_type']));
            $documentableId = $data['documentable_id'];

            // Define the S3 path
            $filePath = "documents/{$documentableType}/{$documentableId}/{$fileName}";

            // Store the file in S3 private folder
            Storage::disk('s3')->put($filePath, $file->get(), 'private');

            // Add file details to data
            $data['name'] = $data['name'] ?? $file->getClientOriginalName();
            $data['file_path'] = $filePath;
            $data['file_type'] = $file->getMimeType();
            $data['file_size'] = $file->getSize();
        }

        // Set uploaded_by_id if not provided
        if (!isset($data['uploaded_by_id'])) {
            $data['uploaded_by_id'] = auth()->id();
        }

        return Document::create($data);
    }

    /**
     * Get a document by ID
     *
     * @param int $id Document ID
     * @return Document|null
     */
    public function getDocument(int $id)
    {
        return Document::find($id);
    }

    /**
     * Get documents by documentable type and ID
     *
     * @param string $type Documentable type
     * @param int $id Documentable ID
     * @return Collection
     */
    public function getDocumentsByDocumentable(string $type, int $id)
    {
        return Document::where('documentable_type', $type)
            ->where('documentable_id', $id)
            ->get();
    }

    /**
     * Get all documents
     *
     * @return Collection
     */
    public function getDocuments()
    {
        return Document::all();
    }

    /**
     * Update a document
     *
     * @param array $data Document data
     * @param int $id Document ID
     * @param UploadedFile|null $file The uploaded file
     * @return Document|null
     */
    public function updateDocument(array $data, int $id, ?UploadedFile $file = null)
    {
        $document = $this->getDocument($id);

        if (!$document) {
            return null;
        }

        if ($file) {
            // Delete the old file if it exists
            if ($document->file_path) {
                Storage::disk('s3')->delete($document->file_path);
            }

            // Generate a unique file name
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Get the documentable type and ID for the folder structure
            $documentableType = Str::lower(class_basename($document->documentable_type));
            $documentableId = $document->documentable_id;

            // Define the S3 path
            $filePath = "documents/{$documentableType}/{$documentableId}/{$fileName}";

            // Store the file in S3 private folder
            Storage::disk('s3')->put($filePath, $file->get(), 'private');

            // Update file details
            $data['name'] = $data['name'] ?? $file->getClientOriginalName();
            $data['file_path'] = $filePath;
            $data['file_type'] = $file->getMimeType();
            $data['file_size'] = $file->getSize();
        }

        $document->update($data);

        return $document;
    }

    /**
     * Delete a document
     *
     * @param int $id Document ID
     * @return Document|null
     */
    public function deleteDocument(int $id)
    {
        $document = $this->getDocument($id);

        if (!$document) {
            return null;
        }

        // Delete the file from S3
        if ($document->file_path) {
            Storage::disk('s3')->delete($document->file_path);
        }

        $document->delete();

        return $document;
    }

    /**
     * Generate a presigned URL for a document
     *
     * @param int $id Document ID
     * @param int $expiresIn Expiration time in seconds
     * @return string|null
     */
    public function getPresignedUrl(int $id, int $expiresIn = 300)
    {
        $document = $this->getDocument($id);

        if (!$document || !$document->file_path) {
            return null;
        }

        // Generate a presigned URL for the S3 object
        $s3Client = Storage::disk('s3')->getClient();
        $bucket = config('filesystems.disks.s3.bucket');

        $command = $s3Client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $document->file_path,
        ]);

        $request = $s3Client->createPresignedRequest($command, "+{$expiresIn} seconds");

        // Update the document with the expiration time
        $document->update([
            'presigned_url_expires_at' => now()->addSeconds($expiresIn),
        ]);

        return (string) $request->getUri();
    }
}
