# Authentication API

## POST /api/v1/auth/login
- Accepts email/password
- Returns token and user profile

## POST /api/v1/auth/logout
- Invalidates token

> **Note:** Use tenant‑aware middleware to ensure subdomain context.
