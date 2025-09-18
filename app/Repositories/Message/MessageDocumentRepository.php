<?php

namespace App\Repositories\Message;

use App\Contracts\MessageDocumentRepositoryInterface;
use App\Models\MessageDocument;
use Exception;

class MessageDocumentRepository implements MessageDocumentRepositoryInterface
{
    /**
     * Attach a document to a message.
     *
     * @param array $data
     * @return MessageDocument
     */
    public function attachDocument(array $data): MessageDocument
    {
        return MessageDocument::create($data);
    }

    /**
     * Detach a document from a message.
     *
     * @param int $messageDocumentId
     * @return void
     * @throws Exception
     */
    public function detachDocument(int $messageDocumentId): void
    {
        $messageDocument = MessageDocument::findOrFail($messageDocumentId);
        $messageDocument->delete();
    }

    /**
     * Get a message document by ID.
     *
     * @param int $messageDocumentId
     * @return MessageDocument|null
     */
    public function getMessageDocument(int $messageDocumentId): ?MessageDocument
    {
        return MessageDocument::find($messageDocumentId);
    }

    /**
     * Get documents attached to a message.
     *
     * @param int $messageId
     * @return array
     */
    public function getDocumentsByMessage(int $messageId): array
    {
        return MessageDocument::where('message_id', $messageId)
            ->with('document')
            ->get()
            ->toArray();
    }

    /**
     * Get messages with a specific document.
     *
     * @param int $documentId
     * @return array
     */
    public function getMessagesByDocument(int $documentId): array
    {
        return MessageDocument::where('document_id', $documentId)
            ->with('message')
            ->get()
            ->toArray();
    }
}
