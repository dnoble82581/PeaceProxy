# Fix for Duplicate Entry Error in Group Chat Creation

## Issue Description

When creating a group chat, the system was encountering the following error:

```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '6-1' for key 'conversation_user.conversation_user_conversation_id_user_id_unique'
(Connection: mysql, SQL: insert into `conversation_user` (`user_id`, `conversation_id`, `created_at`, `updated_at`) values (1, 6, 2025-08-21 05:18:00, 2025-08-21 05:18:00))
```

This error occurred because the system was trying to add the same user (user ID 1) to the same conversation (conversation ID 6) twice, which violated the unique constraint on the `conversation_user` table.

## Root Cause

The issue was in the `createConversationWithUsers` method in the `ConversationCreationService` class. This method:

1. Creates a new conversation
2. Adds the creator (current user) to the conversation
3. Adds all selected users to the conversation

The problem occurred when the creator was also included in the selected users array, which is a common scenario for group chats. In this case, the system would try to add the creator twice:
- Once explicitly as the creator
- Once as part of the selected users loop

Since the `conversation_user` table has a unique constraint on the combination of `conversation_id` and `user_id`, this resulted in a duplicate entry error.

## Solution

The solution was to modify the `createConversationWithUsers` method to check if a selected user is the creator before adding them to the conversation. If the user is the creator, we skip adding them again since they were already added earlier in the method.

### Changes Made

Modified the loop in `ConversationCreationService.php` that adds selected users to the conversation:

```php
// Before:
// Add selected users to the conversation
foreach ($userIds as $userId) {
    $conversation->users()->attach($userId);
}

// After:
// Add selected users to the conversation, excluding the creator to avoid duplicates
foreach ($userIds as $userId) {
    // Skip if this is the creator (already added above)
    if ($userId != $data['created_by']) {
        $conversation->users()->attach($userId);
    }
}
```

## Verification

A test script was created to verify the fix. The script simulates creating a group chat with the creator included in the selected users array. The test confirmed that:

1. The group chat was created successfully
2. The creator was added only once to the conversation, even though they were included in the selected users array
3. All other selected users were added correctly

This fix ensures that the system will no longer encounter the duplicate entry error when creating group chats, even if the creator is included in the selected users array.