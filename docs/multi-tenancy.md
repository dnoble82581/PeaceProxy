# Multi‑Tenancy

## Model Scoping
- All queries scoped to `tenant_id` via global scopes/traits
- Tenants resolved from subdomain middleware

## Routing
- `central.php` for marketing, signup, admin
- `tenant.php` for app routes bound to tenant context

## Data Separation
- Single database with tenant_id foreign keys
- Background jobs include tenant context
- File paths namespaced by tenant
