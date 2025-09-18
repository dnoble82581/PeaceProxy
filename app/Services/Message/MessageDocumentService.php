<?php

namespace App\Services\Message;

use App\Contracts\MessageDocumentRepositoryInterface;
use App\DTOs\MessageDocument\MessageDocumentDTO;
use App\Events\Message\DocumentAttachedEvent;
use App\Models\MessageDocument;

class MessageDocumentService
{
    public function __construct(protected MessageDocumentRepositoryInterface $messageDocumentRepository)
    {
    }

    /**
     * Attach a document to a message.
     */
    public function attachDocument(MessageDocumentDTO $messageDocumentDTO): MessageDocument
    {
        $messageDocument = $this->messageDocumentRepository->attachDocument($messageDocumentDTO->toArray());
        $this->addLogEntry($messageDocument);
        $this->broadcastDocumentAttached($messageDocument);

        return $messageDocument;
    }

    /**
     * Detach a document from a message.
     */
    public function detachDocument(int $messageDocumentId): void
    {
        $messageDocument = $this->messageDocumentRepository->getMessageDocument($messageDocumentId);

        if ($messageDocument) {
            $this->messageDocumentRepository->detachDocument($messageDocumentId);
            // Optional: Add log entry for document detachment
        }
    }

    /**
     * Get documents attached to a message.
     */
    public function getDocumentsByMessage(int $messageId): array
    {
        return $this->messageDocumentRepository->getDocumentsByMessage($messageId);
    }

    private function addLogEntry(MessageDocument $messageDocument): void
    {
        $user = auth()->user();
        $document = $messageDocument->document;

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'message.document.attached',
            headline: "{$user->name} attached a document to a message",
            about: $messageDocument,      // loggable target
            by: $user,                    // actor
            description: "Document: {$document->name}",
            properties: [
                'message_id' => $messageDocument->message_id,
                'document_id' => $messageDocument->document_id,
                'document_name' => $document->name,
                'document_type' => $document->file_type,
            ],
        );
    }

    public function broadcastDocumentAttached(MessageDocument $messageDocument): bool
    {
        if ($messageDocument) {
            event(new DocumentAttachedEvent($messageDocument));
            return true;
        }

        return false;
    }
}
