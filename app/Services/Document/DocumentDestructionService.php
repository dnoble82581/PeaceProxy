<?php

namespace App\Services\Document;

use App\Contracts\DocumentRepositoryInterface;
use App\Events\Document\DocumentDestroyedEvent;
use App\Models\Document;
use App\Services\Log\LogService;

class DocumentDestructionService
{
    public function __construct(
        protected DocumentRepositoryInterface $documentRepository,
        protected ?LogService $logService = null
    ) {
        $this->logService = $logService ?? app(LogService::class);
    }

    /**
     * Delete all documents for a subject
     *
     * @return int Number of documents deleted
     */
    public function deleteSubjectDocuments(int $subjectId): int
    {
        return $this->deleteDocumentsByDocumentable('App\\Models\\Subject', $subjectId);
    }

    /**
     * Delete all documents for a documentable entity
     *
     * @return int Number of documents deleted
     */
    public function deleteDocumentsByDocumentable(string $type, int $id): int
    {
        $documents = $this->documentRepository->getDocumentsByDocumentable($type, $id);
        $count = 0;

        foreach ($documents as $document) {
            if ($this->deleteDocument($document->id)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Delete a document
     */
    public function deleteDocument(int $documentId): ?Document
    {
        // Get the document before deleting it
        $document = $this->documentRepository->getDocument($documentId);

        // If the document does not exist, nothing to delete
        if (! $document) {
            return null;
        }

        // Prepare details for event/logging before deletion
        $details = [
            'fileType' => $document->file_type,
            'storageDisk' => $document->storage_disk,
            'uploadedById' => $document->uploaded_by,
        ];

        // Fire event and add log entry with the loaded document context
        event(new DocumentDestroyedEvent($document->documentable_id, $details));
        $this->addLogEntry($document);

        return $this->documentRepository->deleteDocument($documentId);
    }

    /**
     * Add a log entry for document deletion
     *
     * @return mixed
     */
    private function addLogEntry(Document $document)
    {
        $user = auth()->user();

        return $this->logService->write(
            tenantId: tenant()->id,
            event: 'document.deleted',
            headline: "{$user->name} deleted a document",
            about: $document,      // loggable target
            by: $user,            // actor
            description: str($document->name)->limit(140),
            properties: [
                'negotiation_id' => $document->negotiation_id,
                'documentable_type' => $document->documentable_type,
                'documentable_id' => $document->documentable_id,
                'file_type' => $document->file_type,
                'file_size' => $document->file_size,
            ],
        );
    }

    /**
     * Delete all documents for a user
     *
     * @return int Number of documents deleted
     */
    public function deleteUserDocuments(int $userId): int
    {
        return $this->deleteDocumentsByDocumentable('App\\Models\\User', $userId);
    }

    /**
     * Delete all documents for a negotiation
     *
     * @return int Number of documents deleted
     */
    public function deleteNegotiationDocuments(int $negotiationId): int
    {
        return $this->deleteDocumentsByDocumentable('App\\Models\\Negotiation', $negotiationId);
    }
}
