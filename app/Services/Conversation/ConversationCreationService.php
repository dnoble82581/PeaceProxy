<?php

namespace App\Services\Conversation;

use App\Contracts\ConversationRepositoryInterface;
use App\DTOs\Conversation\ConversationDTO;
use App\Models\Conversation;

class ConversationCreationService
{
    public function __construct(protected ConversationRepositoryInterface $conversationRepository)
    {
    }

    /**
     * Create a new conversation using DTO.
     */
    public function createConversation(ConversationDTO $conversationDTO): Conversation
    {
        return $this->conversationRepository->createConversation($conversationDTO->toArray());
    }

    /**
     * Create a new conversation
     *
     * @param  array  $data  The conversation data
     * @param  array  $userIds  The IDs of users to add to the conversation
     * @return Conversation The created conversation
     */
    public function createConversationWithUsers(array $data, array $userIds): Conversation
    {
        $conversation = $this->conversationRepository->createConversation([
            'tenant_id' => $data['tenant_id'],
            'created_by' => $data['created_by'],
            'negotiation_id' => $data['negotiation_id'],
            'type' => $data['type'],
            'name' => $data['type'] === 'group' ? $data['name'] : null,
            'is_active' => true,
        ]);

        // Add current user to the conversation
        $conversation->users()->attach($data['created_by']);

        // Add selected users to the conversation, excluding the creator to avoid duplicates
        foreach ($userIds as $userId) {
            // Skip if this is the creator (already added above)
            if ($userId != $data['created_by']) {
                $conversation->users()->attach($userId);
            }
        }

        event(new \App\Events\Conversation\ConversationCreatedEvent($conversation));

        return $conversation;
    }

    /**
     * Ensure the public conversation exists for a tenant and negotiation
     *
     * @param  int  $tenantId  The tenant ID to ensure the public conversation for
     * @param  int  $userId  The ID of the user creating the conversation
     * @param  int  $negotiationId  The negotiation ID to ensure the public conversation for
     * @return Conversation The public conversation
     */
    public function ensurePublicConversation(int $tenantId, int $userId, int $negotiationId): Conversation
    {
        $publicConversation = app(\App\Services\Conversation\ConversationFetchingService::class)
            ->getPublicConversation($tenantId, $negotiationId);

        if (! $publicConversation) {
            // Create public conversation
            $publicConversation = $this->conversationRepository->createConversation([
                'tenant_id' => $tenantId,
                'created_by' => $userId,
                'negotiation_id' => $negotiationId,
                'type' => 'public',
                'name' => 'Public Chat',
                'is_active' => true,
            ]);

            // Add all users from the tenant to the public conversation
            $users = \App\Models\User::where('tenant_id', $tenantId)->get();
            foreach ($users as $user) {
                $publicConversation->users()->attach($user->id);
            }
        } else {
            // Make sure current user is in the public conversation
            if (! $publicConversation->users()->where('user_id', $userId)->exists()) {
                $publicConversation->users()->attach($userId);
            }
        }

        return $publicConversation;
    }
}
