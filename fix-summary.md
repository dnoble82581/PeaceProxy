# Fix Summary: Conversation Creation Type Error

## Issue
Type error when creating a new conversation: 'Cannot assign string to property Livewire\Volt\Component@anonymous::$selectedUserIds of type array'

## Root Cause
Radio buttons in private conversations were trying to assign a string value directly to the `$selectedUserIds` array property.

## Fix
Modified the form input binding for radio buttons to use array indexing (`selectedUserIds.0`) instead of direct assignment, ensuring type compatibility while preserving functionality.

## Files Changed
- `/resources/views/livewire/pages/negotiation/chat/negotiation-chat.blade.php`

## Testing
This fix ensures that both private conversations (using radio buttons) and group conversations (using checkboxes) can be created without type errors.