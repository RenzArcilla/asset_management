# Docker Setup for Production Image

This document describes the Docker environment for this project: architecture, setup steps, environment configuration, and troubleshooting notes for issues encountered during initial setup.

## Stack Overview

| Component | Image / Base | Purpose |
|---|---|---|
| `app` | Custom (`php:8.4-fpm`) | Laravel application (PHP-FPM) |
| `horizon` | Same image as `app` | Queue worker supervisor (Laravel Horizon) |
| `reverb` | Same image as `app` | WebSocket server (Laravel Reverb, broadcasting) |
| `nginx` | `nginx:1-alpine` | Reverse proxy / web server |
| `db` | `mysql:8.0` | Primary relational database |
| `redis` | `redis:7.2-alpine` | Cache, session store, queue backend (Horizon) |

`app`, `horizon`, and `reverb` all share a **single built image** (`asset_management-app:latest`). Only `app` declares a `build:` block in `docker-compose.yml`; `horizon` and `reverb` reference the same tagged image via `depends_on: [app]`. This avoids building the same Dockerfile three times and keeps disk usage to one ~1GB image instead of three.

## Directory Structure (Docker-relevant)

```
.
├── Dockerfile
├── docker-compose.yml
├── .dockerignore
├── .env                  # local development (NOT used by containers)
├── .env.docker           # environment variables injected into containers
├── nginx/
│   ├── nginx.conf
│   └── conf.d/
│       └── default.conf
└── scripts/
    └── php-fpm-entrypoint
```

## Dockerfile (Multi-Stage Build)

The `Dockerfile` uses three stages:

1. **`vendor`** (`composer:2`)
    - resolves PHP dependencies via `composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs`. Only re-runs when `composer.json`/`composer.lock` change.
2. **`assets`** (`node:20-alpine`) 
    - installs npm dependencies and runs `npm run build` (Vite/Tailwind). Only re-runs when `package.json`/`package-lock.json` change, or app source changes (since it needs source to build against).
3. **Final stage** (`php:8.4-fpm`)
    - assembles the runtime image: installs PHP extensions, copies in `vendor/` and `public/build/` from the earlier stages, sets ownership, and drops to a non-root user.

### Why multi-stage?

Without staging, any source code change would invalidate the Docker layer cache and force a full `composer install` + `npm ci` re-run on every build. Staging isolates dependency resolution from application code changes, dramatically speeding up iterative builds.

### PHP version

Pinned to `php:8.4-fpm`. This **must** match the minimum PHP version required by `composer.lock`. If you bump Laravel or a dependency that raises the PHP floor, update this base image accordingly. A mismatch here surfaces as a `platform_check.php` error during `composer dump-autoload` in the final stage (the `vendor` stage's `--ignore-platform-reqs` flag hides this until the final stage, which does not skip the check).

### Non-root user & UID/GID matching

The container runs as `www-data`, not root. Because `./public` is bind-mounted from the host (see `docker-compose.yml`), the container's `www-data` UID/GID must match the host user's UID/GID, or the container will be unable to write to bind-mounted paths (e.g. `php artisan storage:link` will fail with `symlink(): Permission denied`).

This is handled via build args:

```dockerfile
ARG UID=1000
ARG GID=1000
RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data
```

Before building, confirm your host UID/GID:

```bash
id -u
id -g
```

If they are not `1000`/`1000`, update the `args:` block for the `app` service in `docker-compose.yml` accordingly.

### Extensions installed

`pcntl`, `pdo`, `pdo_mysql`, `gd`, `bcmath`, `zip`, `opcache`, `mbstring`, `exif`, `intl`, `redis` (via PECL).

`redis` requires `autoconf`, `g++`, and `make` to compile — these are installed, used, then purged in the same `RUN` layer to keep the final image lean.

## Environment Files

Two separate env files are used:

| File | Used by | Purpose |
|---|---|---|
| `.env` | Local dev (`php artisan serve`, host-side tooling)  | `DB_HOST=127.0.0.1`, `REDIS_HOST=127.0.0.1` |
| `.env.docker` | Containers via `env_file:` | `DB_HOST=db`, `REDIS_HOST=redis` (Docker service names) |

Both are gitignored; only `.env.example` / `.env.docker.example` should be committed.

**Key values that must be kept in sync between both files:** `REDIS_PASSWORD` (see `redis` service note above).

## Build & Run

```bash
# Confirm host UID/GID match Dockerfile build args (see above)
id -u
id -g

# Build (only app builds; horizon/reverb reuse the image)
docker compose build

# Start everything
docker compose up -d

# Check status — all services should show "Up", db should show "(healthy)"
docker compose ps
```

### Rebuild Notes

No Hot Replacement Module (HMR):
- The image is a static snapshot from build time (changes require a rebuild).

You do **not** need to rebuild when:
- `nginx/nginx.conf` or `nginx/conf.d/*.conf` change (bind-mounted), use `docker compose restart nginx`
- `.env` / `.env.docker` values change, use `docker compose up -d --force-recreate <service>`



## Verifying a Healthy Stack

```bash
docker compose ps
# All services "Up", db shows "(healthy)"

curl -I http://localhost
# Should return "Server: nginx/..." 200/302 statud expected.

docker compose exec app ls -la public/storage
# Should show a symlink to storage/app/public, not a permission error

docker compose logs horizon --tail=20
# Should show "Horizon started successfully." with no WRONGPASS errors

docker compose logs reverb --tail=20
# Should show "Starting server on 0.0.0.0:8080 (reverb)."
```

## Service Notes
 
### `app`
- Builds the shared image (`asset_management-app:latest`), exposes PHP-FPM on port `9000` internally.
- Entrypoint runs the full setup sequence on boot: wait for DB, `storage:link`, migrations, `optimize:clear` + `optimize`, then hands off to `php-fpm`.
- `env_file: .env.docker`.

### `horizon`
- Reuses the `app` image, overrides `command: php artisan horizon`.

- Requires `laravel/horizon` installed via Composer and `php artisan horizon:install` run beforehand, with `config/horizon.php`'s `environments` key matching whatever `APP_ENV` is set in `.env.docker` (e.g. `local` vs `production`), or Horizon will error on boot.
- Connects to `redis` for queue backend. `REDIS_PASSWORD` must match what's set on the `redis` service (see below).
### `reverb`
- Reuses the `app` image, overrides `command: php artisan reverb:start --host=0.0.0.0 --port=8080`.
- Installed via `php artisan install:broadcasting` → select Laravel Reverb.
- Must bind `0.0.0.0`, not `127.0.0.1`, to be reachable from outside its own container.

### `nginx`
- Uses the stock `nginx:1-alpine` image.
- Config is bind-mounted (`nginx/nginx.conf`, `nginx/conf.d/`) rather than copied into an image, so config edits only need `docker compose restart nginx`, not a rebuild.

### `db`
- Uses the official `mysql:8.0` image (Bitnami removed most free-tier tags from Docker Hub in August 2025).
- Has a healthcheck (`mysqladmin ping`) so other services can be configured to wait for actual DB readiness rather than just container start.
- Data persists in the named volume `db-data` → `/var/lib/mysql`.

### `redis`
- Uses the official `redis:7.2-alpine` image (not Bitnami, same reasoning as `db`).
- Password set via `--requirepass ${REDIS_PASSWORD}` as a command-line flag. The official image doesn't support Bitnami-style `REDIS_PASSWORD`/`ALLOW_EMPTY_PASSWORD` env vars.
- Both `.env` and `.env.docker` must contain the identical redis password value to avoid services fail with `WRONGPASS`.
- `FLUSHDB`/`FLUSHALL` are disabled via `--rename-command` to prevent accidental data loss.