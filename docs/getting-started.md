# Getting Started

## Prerequisites
- PHP 8.3+
- Composer 2+
- Node.js 20+ & PNPM/NPM
- MySQL 8.x or PostgreSQL 14+
- Redis 7+ (queues, cache, broadcasts)
- OpenSSL (for encryption keys)
- [Herd](https://herd.laravel.com/) or Valet/Docker for local dev

## Quick Setup
```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
# Frontend
pnpm install
pnpm run build # or: pnpm run dev
# Queues & websockets
php artisan queue:work
php artisan reverb:start
php artisan serve
```

## Environment
Document the relevant `.env` keys here:

```env
APP_URL=http://tenant.test
APP_ENV=local
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
FILESYSTEM_DISK=s3
AWS_BUCKET=...
AWS_PUBLIC_BUCKET=...
```

## First Run
- Visit the central domain to register a tenant.
- Login and you’ll be redirected to `https://<tenant>.<domain>`.
- Verify real‑time features: open two browsers and send a chat message.
