# Chat System Documentation

## Overview

The chat system allows users to communicate in real-time within the negotiation platform. It supports three types of conversations:

1. **Public Chat**: A single conversation that includes all users under the current tenant.
2. **Private Chats**: One-on-one conversations between two users.
3. **Group Chats**: Conversations with multiple users, created by a user who can invite others.

The system also supports "whispers" - private messages within public or group chats that are only visible to the sender and the recipient.

## Database Structure

The chat system uses three main models:

### Conversation

Represents a chat conversation with the following attributes:
- `tenant_id`: The tenant the conversation belongs to
- `created_by`: The user who created the conversation
- `name`: Optional name for group chats
- `type`: Type of conversation ('public', 'private', or 'group')
- `is_active`: Whether the conversation is active or closed

### Message

Represents a message within a conversation with the following attributes:
- `conversation_id`: The conversation the message belongs to
- `user_id`: The user who sent the message
- `content`: The message content
- `whisper_to`: Optional user ID for whisper messages
- `is_whisper`: Whether the message is a whisper

### ConversationUser

A pivot model that tracks user participation in conversations with the following attributes:
- `conversation_id`: The conversation
- `user_id`: The user
- `joined_at`: When the user joined the conversation
- `left_at`: When the user left the conversation (if applicable)

## Features

### Conversation Management

- **Public Chat**: Automatically created for each tenant and includes all users.
- **Private Chats**: Users can create private chats with one other user.
- **Group Chats**: Users can create group chats and invite multiple users.
- **Closing Conversations**: Users can close conversations they created (except the public chat).

### Messaging

- **Regular Messages**: Visible to all users in the conversation.
- **Whispers**: Private messages within a conversation, only visible to the sender and recipient.

### User Interface

- **Tab Navigation**: Users can switch between public, private, and group conversations.
- **Conversation List**: Shows available conversations based on the selected tab.
- **Message Display**: Shows messages with user avatars, names, timestamps, and content.
- **Whisper UI**: Allows users to select recipients for whisper messages.
- **New Conversation Modal**: Interface for creating new private or group conversations.

## Usage

### Switching Between Conversation Types

Use the tabs at the top of the chat interface to switch between public, private, and group conversations.

### Creating a New Conversation

1. Click the "New" button in the top-right corner.
2. Select the conversation type (private or group).
3. For group chats, enter a name.
4. Select the user(s) to include in the conversation.
5. Click "Create".

### Sending Messages

1. Select a conversation from the sidebar.
2. Type your message in the input field at the bottom.
3. Press Enter or click the "Send" button.

### Sending Whispers

1. In a public or group chat, click on a user's name in the "Whisper to" section.
2. Type your message in the input field.
3. Press Enter or click the "Send" button.
4. The whisper will only be visible to you and the selected user.

### Closing a Conversation

If you created a conversation, you can close it by clicking the "X" button in the conversation header or in the sidebar.

## Implementation Details

The chat system is implemented using:

- Laravel models and migrations for the database structure
- Livewire for real-time functionality
- Alpine.js for reactive UI components
- Tailwind CSS for styling

The main component is located at:
`/resources/views/livewire/pages/negotiation/chat/negotiation-chat.blade.php`

## Extending the System

To extend the chat system, you can:

1. Add more conversation types by extending the `type` enum in the Conversation model.
2. Add more message types by extending the Message model.
3. Add more UI features by modifying the negotiation-chat.blade.php file.