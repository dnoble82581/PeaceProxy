# Architecture

## Overview
- **Framework:** Laravel 12
- **Frontend:** Blade + Livewire Volt + Alpine.js, Tailwind CSS v4, TALLStack UI components
- **Realtime:** Laravel Reverb (WebSockets)
- **Storage:** S3 (public avatars, private documents)
- **Tenancy:** Subdomain‑based single‑DB multi‑tenancy

## Folders & Conventions
```
app/
  Actions/
  DTOs/
  Http/
    Controllers/
    Livewire/
      (Volt components)
  Models/
  Policies/
  Repositories/
  Services/
resources/
  views/ (Blade + Volt)
  css, js (Tailwind v4, Alpine)
routes/
  tenant.php
  central.php
```

## Data Flow
1. Controller/Volt receives request
2. Validates & authorizes (Policies)
3. Service/Repository handles business logic and persistence
4. Events dispatched → broadcast via Reverb if needed
5. Livewire updates UI reactively

## Eventing
- Domain events (e.g., `MessageSent`, `UserTyping`)
- Broadcast channels: `private-tenant.{tenantId}.chat.{conversationId}`
- Presence via backend events when possible

## Testing
- Pest or PHPUnit
- Feature tests for tenant scoping
- Livewire component tests
