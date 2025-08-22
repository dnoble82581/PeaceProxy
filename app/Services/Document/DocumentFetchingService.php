<?php

namespace App\Services\Document;

use App\Contracts\DocumentRepositoryInterface;
use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;

class DocumentFetchingService
{
    /**
     * @param DocumentRepositoryInterface $documentRepository
     */
    public function __construct(protected DocumentRepositoryInterface $documentRepository)
    {
    }

    /**
     * Get a document by ID
     *
     * @param int $documentId
     * @return Document|null
     */
    public function getDocumentById(int $documentId): ?Document
    {
        return $this->documentRepository->getDocument($documentId);
    }

    /**
     * Get documents by documentable type and ID
     *
     * @param string $type
     * @param int $id
     * @return Collection
     */
    public function getDocumentsByDocumentable(string $type, int $id): Collection
    {
        return $this->documentRepository->getDocumentsByDocumentable($type, $id);
    }

    /**
     * Get all documents
     *
     * @return Collection
     */
    public function getAllDocuments(): Collection
    {
        return $this->documentRepository->getDocuments();
    }

    /**
     * Get a presigned URL for a document
     *
     * @param int $documentId
     * @param int $expiresIn
     * @return string|null
     */
    public function getDocumentPresignedUrl(int $documentId, int $expiresIn = 300): ?string
    {
        return $this->documentRepository->getPresignedUrl($documentId, $expiresIn);
    }
}
