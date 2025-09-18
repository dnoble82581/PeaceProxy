<?php

namespace App\Services\Message;

use App\Contracts\MessageReactionRepositoryInterface;
use App\Events\Message\MessageReactionChangedEvent;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\User;
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

    public function getMessageReactionCounts(int $messageId): array
    {
        return DB::table('message_reactions')
            ->where('message_id', $messageId)
            ->select('reaction_type', DB::raw('count(*) as count'))
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->all();
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

            $oldEmoji = $current?->reaction_type;   // ← capture before changes
            $changed = false;

            // CASE A: clear reaction (toggle off)
            if (is_null($emoji)) {
                if ($current) {
                    $current->delete();
                    $changed = true;
                }

                // fresh counts after change
                $message = Message::findOrFail($messageId);
                $reactor = User::findOrFail($userId);
                /** @var MessageReactionService $svc */
                $svc = app(MessageReactionService::class);

                $oldEmojiCount = $oldEmoji
                    ? $svc->getMessageReactionCount($messageId, $oldEmoji)
                    : null;

                // newEmoji is null because it was cleared
                event(new MessageReactionChangedEvent(
                    message: $message,
                    emoji: null,
                    oldEmoji: $oldEmoji,
                    reactor: $reactor,
                    oldCount: $oldEmojiCount,
                    newCount: null,
                ));

                return ['changed' => $changed, 'emoji' => null];
            }

            // CASE B: set or switch reaction
            if ($current) {
                if ($current->reaction_type !== $emoji) {
                    // switching emoji
                    $current->update(['reaction_type' => $emoji]);
                    $changed = true;
                } else {
                    // clicking the same emoji again? if you want that to clear instead, you can:
                    // $current->delete(); $emoji = null; $changed = true;
                }
            } else {
                $negotiationId = Message::findOrFail($messageId)->negotiation_id;

                MessageReaction::create([
                    'tenant_id' => tenant()->id,
                    'negotiation_id' => $negotiationId,
                    'reaction_type' => $emoji,
                    'message_id' => $messageId,
                    'user_id' => $userId,
                    'type' => $emoji, // (optional/duplicated field)
                ]);

                $changed = true;
            }

            // Broadcast authoritative counts for both the new and old emoji
            $message = Message::findOrFail($messageId);
            $reactor = User::findOrFail($userId);
            /** @var MessageReactionService $svc */
            $svc = app(MessageReactionService::class);

            $newEmoji = $emoji;
            $newCount = $newEmoji
                ? $svc->getMessageReactionCount($messageId, $newEmoji)
                : null;

            $oldCount = $oldEmoji
                ? $svc->getMessageReactionCount($messageId, $oldEmoji)
                : null;

            event(new MessageReactionChangedEvent(
                message: $message,
                emoji: $newEmoji,
                oldEmoji: $oldEmoji,
                reactor: $reactor,
                oldCount: $oldCount,
                newCount: $newCount,
            ));

            return ['changed' => $changed, 'emoji' => $emoji];
        });
    }

    public function getMessageReactionCount(int $messageId, string $emoji): int
    {
        return MessageReaction::query()
            ->where('message_id', $messageId)
            ->where('reaction_type', $emoji)
            ->count();
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
