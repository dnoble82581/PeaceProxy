<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface DocumentRepositoryInterface
{
    /**
     * Create a new document
     *
     * @param array $data Document data
     * @param UploadedFile|null $file The uploaded file
     * @return mixed
     */
    public function createDocument(array $data, ?UploadedFile $file = null);

    /**
     * Get a document by ID
     *
     * @param int $id Document ID
     * @return mixed
     */
    public function getDocument(int $id);

    /**
     * Get documents by documentable type and ID
     *
     * @param string $type Documentable type
     * @param int $id Documentable ID
     * @return mixed
     */
    public function getDocumentsByDocumentable(string $type, int $id);

    /**
     * Get all documents
     *
     * @return mixed
     */
    public function getDocuments();

    /**
     * Update a document
     *
     * @param array $data Document data
     * @param int $id Document ID
     * @param UploadedFile|null $file The uploaded file
     * @return mixed
     */
    public function updateDocument(array $data, int $id, ?UploadedFile $file = null);

    /**
     * Delete a document
     *
     * @param int $id Document ID
     * @return mixed
     */
    public function deleteDocument(int $id);

    /**
     * Generate a presigned URL for a document
     *
     * @param int $id Document ID
     * @param int $expiresIn Expiration time in seconds
     * @return string
     */
    public function getPresignedUrl(int $id, int $expiresIn = 300);
}
