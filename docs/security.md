# Security

## Authentication & Authorization
- Laravel Fortify/Jetstream or custom flows
- Authorization via Policies and Gates

## Data Protection
- E2E messaging (decrypt only at destination) for message content
- At rest: S3 SSE, DB encryption for sensitive fields

## Secrets Management
- Use parameter store or secrets manager, not raw `.env` in CI

## Auditing & Compliance
- Model activity log (created/updated by, IP)
- Retention and export policies for law‑enforcement customers

## Threat Model
- XSS: escape output, Livewire entanglement rules
- CSRF: built‑in middleware
- SSRF: validate URLs before fetching
