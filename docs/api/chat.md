# Chat API

## GET /api/v1/tenants/{tenant}/conversations
List conversations for the authenticated user.

## POST /api/v1/tenants/{tenant}/conversations/{id}/messages
Create a message in a conversation.

### Broadcast Events
- `MessageSent`
- `MessageRead`
