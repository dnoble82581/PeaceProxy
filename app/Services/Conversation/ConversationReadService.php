<?php

namespace App\Services\Conversation;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ConversationReadService
{
    /** @return array<int,int> conversation_id => unread_count */
    public function seedUnreadForNegotiation(User $user, int $negotiationId): array
    {
        // Get user's conversations for this negotiation with their last_read markers
        $rows = DB::table('conversations')
            ->join('conversation_user', 'conversations.id', '=', 'conversation_user.conversation_id')
            ->where('conversations.negotiation_id', $negotiationId)
            ->where('conversation_user.user_id', $user->id)
            ->get([
                'conversations.id as conversation_id',
                'conversation_user.last_read_at',
                'conversation_user.last_read_message_id',
            ]);

        $unread = [];
        foreach ($rows as $r) {
            $q = Message::where('conversation_id', $r->conversation_id)
                ->where('user_id', '!=', $user->id);

            if ($r->last_read_message_id) {
                // Fast path by id if you like
                $q->where('id', '>', $r->last_read_message_id);
            } elseif ($r->last_read_at) {
                $q->where('created_at', '>', $r->last_read_at);
            }

            $unread[$r->conversation_id] = $q->count();
        }

        return $unread;
    }

    public function markConversationRead(User $user, int $conversationId): void
    {
        $lastId = Message::where('conversation_id', $conversationId)->max('id');

        DB::table('conversation_user')
            ->where('user_id', $user->id)
            ->where('conversation_id', $conversationId)
            ->update([
                'last_read_at' => now(),
                'last_read_message_id' => $lastId,
            ]);
    }
}
