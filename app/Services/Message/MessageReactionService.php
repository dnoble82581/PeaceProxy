<?php

namespace App\Services\Message;

use App\Contracts\MessageReactionRepositoryInterface;
use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Support\Facades\DB;

class MessageReactionService
{
    public function __construct(protected MessageReactionRepositoryInterface $messageReactionRepository)
    {
    }

    /**
     * Add a reaction to a message.
     */
    public function getUserReactionType(int $messageId): ?string
    {
        $userId = auth()->id();
        if (! $userId) {
            return null;
        }

        // If you have multi-tenancy columns, also add ->where('tenant_id', tenant()->id)
        $type = MessageReaction::query()
            ->where('message_id', $messageId)
            ->where('user_id', $userId)
            ->value('reaction_type');

        return $type ?: null;
    }

    /**
     * Get reactions grouped by type for a message.
     */
    public function getReactionsGroupedByType(int $messageId): array
    {
        $reactions = $this->messageReactionRepository->getReactionsByMessage($messageId);
        $grouped = [];

        foreach ($reactions as $reaction) {
            $type = $reaction['reaction_type'];
            if (! isset($grouped[$type])) {
                $grouped[$type] = [
                    'count' => 0,
                    'users' => [],
                ];
            }

            $grouped[$type]['count']++;
            $grouped[$type]['users'][] = [
                'id' => $reaction['user_id'],
                'name' => $reaction['user']['name'] ?? 'Unknown User',
            ];
        }

        return $grouped;
    }

    /**
     * Get reactions for a message.
     */
    public function getReactionsByMessage(int $messageId): array
    {
        return $this->messageReactionRepository->getReactionsByMessage($messageId);
    }

    /**
     * @throws \Throwable
     */
    public function setReaction(int $messageId, int $userId, ?string $emoji): array
    {
        return DB::transaction(function () use ($messageId, $userId, $emoji) {
            $current = MessageReaction::query()
                ->where('message_id', $messageId)
                ->where('user_id', $userId)
                ->first();

            $changed = false;

            if (is_null($emoji)) {
                if ($current) {
                    $current->delete();
                    $changed = true;
                }

                return ['changed' => $changed, 'emoji' => null];
            }

            if ($current) {
                if ($current->reaction_type !== $emoji) {
                    $current->update(['reaction_type' => $emoji]);
                    $changed = true;
                }
            } else {
                $negotiationId = Message::findOrFail($messageId)->negotiation_id;
                MessageReaction::create([
                    'tenant_id' => tenant()->id,
                    'negotiation_id' => $negotiationId,
                    'reaction_type' => $emoji,
                    'message_id' => $messageId,
                    'user_id' => $userId,
                    'type' => $emoji,
                ]);
                $changed = true;
            }

            return ['changed' => $changed, 'emoji' => $emoji];
        });
    }



    private function addLogEntry(MessageReaction $messageReaction, string $action): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: "message.reaction.{$action}",
            headline: "{$user->name} {$action} a reaction to a message",
            about: $messageReaction,      // loggable target
            by: $user,                    // actor
            description: "Reaction: {$messageReaction->reaction_type}",
            properties: [
                'message_id' => $messageReaction->message_id,
                'reaction_type' => $messageReaction->reaction_type,
            ],
        );
    }


}
