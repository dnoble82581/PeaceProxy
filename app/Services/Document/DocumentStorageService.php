<?php

namespace App\Services\Document;

use App\Contracts\DocumentRepositoryInterface;
use App\Models\Document;
use Illuminate\Http\UploadedFile;

class DocumentStorageService
{
    /**
     * @param DocumentRepositoryInterface $documentRepository
     */
    public function __construct(protected DocumentRepositoryInterface $documentRepository)
    {
    }

    /**
     * Create a new document
     *
     * @param array $data
     * @param UploadedFile|null $file
     * @return Document
     */
    public function createDocument(array $data, ?UploadedFile $file = null): Document
    {
        // Ensure tenant_id is set
        if (!isset($data['tenant_id'])) {
            $data['tenant_id'] = auth()->user()->tenant_id;
        }

        // Ensure uploaded_by_id is set
        if (!isset($data['uploaded_by_id'])) {
            $data['uploaded_by_id'] = auth()->id();
        }

        return $this->documentRepository->createDocument($data, $file);
    }

    /**
     * Update an existing document
     *
     * @param array $data
     * @param int $documentId
     * @param UploadedFile|null $file
     * @return Document|null
     */
    public function updateDocument(array $data, int $documentId, ?UploadedFile $file = null): ?Document
    {
        return $this->documentRepository->updateDocument($data, $documentId, $file);
    }

    /**
     * Create a document for a subject
     *
     * @param array $data
     * @param int $subjectId
     * @param UploadedFile|null $file
     * @return Document
     */
    public function createSubjectDocument(array $data, int $subjectId, ?UploadedFile $file = null): Document
    {
        $data['documentable_type'] = 'App\\Models\\Subject';
        $data['documentable_id'] = $subjectId;

        return $this->createDocument($data, $file);
    }

    /**
     * Create a document for a user
     *
     * @param array $data
     * @param int $userId
     * @param UploadedFile|null $file
     * @return Document
     */
    public function createUserDocument(array $data, int $userId, ?UploadedFile $file = null): Document
    {
        $data['documentable_type'] = 'App\\Models\\User';
        $data['documentable_id'] = $userId;

        return $this->createDocument($data, $file);
    }

    /**
     * Create a document for a negotiation
     *
     * @param array $data
     * @param int $negotiationId
     * @param UploadedFile|null $file
     * @return Document
     */
    public function createNegotiationDocument(array $data, int $negotiationId, ?UploadedFile $file = null): Document
    {
        $data['documentable_type'] = 'App\\Models\\Negotiation';
        $data['documentable_id'] = $negotiationId;
        $data['negotiation_id'] = $negotiationId;

        return $this->createDocument($data, $file);
    }
}
