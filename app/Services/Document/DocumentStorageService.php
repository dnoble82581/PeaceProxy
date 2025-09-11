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
    public function __construct(
        protected DocumentRepositoryInterface $documentRepository,
        protected ?LogService $logService = null
    ) {
        $this->logService = $logService ?? app(LogService::class);
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

        $document = $this->documentRepository->createDocument($data, $file);

        // Log the document creation
        $log = $this->addLogEntry($document, 'document.created', 'created');
        logger($log);

        return $document;
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
        $document = $this->documentRepository->updateDocument($data, $documentId, $file);

        if (!$document) {
            return null;
        }

        // Log the document update
        $log = $this->addLogEntry($document, 'document.updated', 'updated');
        logger($log);

        return $document;
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

    /**
     * Add a log entry for document operations
     *
     * @param Document $document
     * @param string $event
     * @param string $action
     * @return mixed
     */
    private function addLogEntry(Document $document, string $event, string $action)
    {
        $user = auth()->user();

        return $this->logService->write(
            tenantId: tenant()->id,
            event: $event,
            headline: "{$user->name} {$action} a document",
            about: $document,      // loggable target
            by: $user,            // actor
            description: str($document->name)->limit(140),
            properties: [
                'negotiation_id' => $document->negotiation_id,
                'documentable_type' => $document->documentable_type,
                'documentable_id' => $document->documentable_id,
                'file_type' => $document->file_type,
                'file_size' => $document->file_size,
                'is_private' => $document->is_private,
            ],
        );
    }
}
