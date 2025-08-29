# Real‑time & Events

## Broadcasting
- Driver: **reverb**
- Channels:
  - Private: `private-tenant.{tenantId}.*`
  - Presence: `presence-tenant.{tenantId}.online`

## Frontend
- Livewire Volt components listen for events
- Minimal Alpine glue for UI interactions

## Performance
- Debounce chat typing
- Paginate messages (infinite scroll)
- Use `loadMissing()` and selective eager loading
