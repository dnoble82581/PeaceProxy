# Deployment

## Targets
- **Production:** Linux host with PHP-FPM, Nginx, Redis, Supervisor
- **CI/CD:** GitHub Actions pipeline (build → test → deploy)

## Steps
1. Build assets: `pnpm run build`
2. Composer install with `--no-dev`, cache
3. Run migrations: `php artisan migrate --force`
4. Cache config/routes/views
5. Queue workers via Supervisor
6. Start Reverb server (systemd/supervisor)
7. Warm caches & run smoke tests

## Zero-Downtime
- Use Envoy or Deployer
- Atomic symlink switch
- Horizon for queue monitoring (optional)

## Env Vars Checklist
List production‑critical `.env` keys and rotate secrets policy.
