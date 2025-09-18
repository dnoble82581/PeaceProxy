<?php

namespace App\Contracts;

use App\Models\MessageDocument;

interface MessageDocumentRepositoryInterface
{
    /**
     * Attach a document to a message.
     *
     * @param array $data
     * @return MessageDocument
     */
    public function attachDocument(array $data): MessageDocument;

    /**
     * Detach a document from a message.
     *
     * @param int $messageDocumentId
     * @return void
     */
    public function detachDocument(int $messageDocumentId): void;

    /**
     * Get a message document by ID.
     *
     * @param int $messageDocumentId
     * @return MessageDocument|null
     */
    public function getMessageDocument(int $messageDocumentId): ?MessageDocument;

    /**
     * Get documents attached to a message.
     *
     * @param int $messageId
     * @return array
     */
    public function getDocumentsByMessage(int $messageId): array;

    /**
     * Get messages with a specific document.
     *
     * @param int $documentId
     * @return array
     */
    public function getMessagesByDocument(int $documentId): array;
}
