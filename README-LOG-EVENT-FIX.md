# Log Event Error Fix

## Issue Description

The application was experiencing an error when trying to send a new message:

```
Undefined property: App\Events\Log\LogCreatedEvent::$eventKey
```

## Root Cause

The error occurred because the `WriteActivityLogListener` was registered to handle `LogCreatedEvent` in the `EventServiceProvider`, but it was trying to access properties like `$eventKey` that don't exist in `LogCreatedEvent`.

The `LogCreatedEvent` only has a `$logData` property (which is a `CreateLogDTO` object), but the `WriteActivityLogListener` was expecting an event with properties like `eventKey`, `headline`, `subjectModel`, etc.

## Solution

The solution was to remove `WriteActivityLogListener` from the listeners for `LogCreatedEvent` in the `EventServiceProvider`. This prevents the listener from being called when a `LogCreatedEvent` is dispatched, thus avoiding the error.

### Changes Made

1. Modified `app/Providers/EventServiceProvider.php` to remove `WriteActivityLogListener` from the listeners for `LogCreatedEvent`:

```php
// Before
LogCreatedEvent::class => [
    LogCreatedListener::class,
    WriteActivityLogListener::class,
],

// After
LogCreatedEvent::class => [
    LogCreatedListener::class,
],
```

## Testing

To test that the fix resolves the issue:

1. Ensure your queue worker is running:
   ```bash
   php artisan queue:work
   ```

2. Send a message in the NegotiationChat component

3. Verify that:
   - The message appears in the chat without errors
   - The log entry is created in the database (check the `logs` table)
   - The queue worker processes the job without errors

## Why This Works

The `WriteActivityLogListener` was redundant for `LogCreatedEvent` because:

1. `LogCreatedEvent` is dispatched when a log entry is being created
2. The `ProcessLogCreationJob` already handles creating the log entry
3. Having `WriteActivityLogListener` try to create another log entry was unnecessary and could potentially cause infinite loops or duplicate logs

By removing this listener, we ensure that only the appropriate listeners handle the event, preventing errors and improving the efficiency of the logging system.