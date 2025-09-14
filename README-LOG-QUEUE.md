# Log Queue Implementation

This document describes the implementation of queued log processing in the Peace Proxy application.

## Overview

The Log model has been updated to use a job to queue the event, improving performance by not blocking the main request thread for log creation. This implementation includes:

1. A Log Repository Interface
2. An updated Log Repository implementation
3. A Log Event class
4. A Log Job class for queued processing
5. An updated Log Service with async support
6. A Log Event Listener

## Components

### DTOs

- `CreateLogDTO`: Data Transfer Object for creating log entries

### Repository

- `LogRepositoryInterface`: Interface defining the contract for log repositories
- `LogRepository`: Implementation of the LogRepositoryInterface

### Events

- `LogCreatedEvent`: Event dispatched when a log entry is created

### Jobs

- `ProcessLogCreationJob`: Job for processing log creation asynchronously

### Listeners

- `LogCreatedListener`: Listener for the LogCreatedEvent
- `WriteActivityLogListener`: Existing listener for activity logs

### Services

- `LogService`: Service for creating log entries, now with async support

## Usage Examples

### Synchronous Log Creation (Backward Compatible)

```php
// Inject the LogService
public function __construct(private readonly LogService $logService)
{
}

// Create a log entry synchronously
$log = $this->logService->write(
    tenantId: $tenant->id,
    event: 'user.login',
    headline: 'User logged in',
    about: $user,
    by: $user,
    description: 'User logged in successfully',
    properties: ['ip' => $request->ip()],
    channel: 'auth',
    severity: 'info'
);
```

### Asynchronous Log Creation (New Method)

```php
// Inject the LogService
public function __construct(private readonly LogService $logService)
{
}

// Create a log entry asynchronously
$this->logService->writeAsync(
    tenantId: $tenant->id,
    event: 'user.login',
    headline: 'User logged in',
    about: $user,
    by: $user,
    description: 'User logged in successfully',
    properties: ['ip' => $request->ip()],
    channel: 'auth',
    severity: 'info'
);
```

## Queue Configuration

Make sure your queue worker is running to process the jobs:

```bash
php artisan queue:work
```

You can configure the queue connection in your `.env` file:

```
QUEUE_CONNECTION=redis
```

## Event Listeners

The `LogCreatedEvent` is dispatched when a log entry is created. You can create additional listeners for this event to perform actions such as:

- Sending notifications for critical logs
- Updating real-time dashboards
- Syncing with external monitoring systems

Register your listeners in the `EventServiceProvider`:

```php
protected $listen = [
    LogCreatedEvent::class => [
        LogCreatedListener::class,
        WriteActivityLogListener::class,
        // Add your custom listeners here
    ],
];
```

## Benefits

1. **Improved Performance**: Log creation doesn't block the main request thread
2. **Scalability**: Queue workers can be scaled independently
3. **Reliability**: Failed log creation attempts can be retried
4. **Extensibility**: Additional processing can be added via event listeners