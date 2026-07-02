# Exposing the application via Cloudflare Quick Tunnel

Quick reference for spinning up a free Cloudflare Tunnel to share the dockerized app externally.

> Assumes `trustProxies` (bootstrap/app.php) and the nginx `fastcgi_param HTTP_X_FORWARDED_PROTO` fix are already committed. If they're not in the codebase yet, see "One-time codebase fixes" at the bottom.

## 1. Find your Docker network name

```bash
docker network ls
```

Or inspect a running container directly:

```bash
docker inspect laravel_nginx --format '{{json .NetworkSettings.Networks}}'
```

## 2. Start the quick tunnel

Run cloudflared on the same Docker network, pointed at the nginx service:

```bash
docker run --rm --network <your_network_name> \
  cloudflare/cloudflared:latest tunnel --url http://nginx:80
```

This prints a random URL like:

```
https://random-words-here.trycloudflare.com
```

**Note:** the URL changes every time this container restarts. Keep the terminal open (or check `docker container ls` for its name, e.g. `asset_management_default`, to grab logs later).

## 3. Update `.env.docker` with the new tunnel URL

```dotenv
APP_URL=https://random-words-here.trycloudflare.com
```

## 4. Recreate the app containers so they pick up the new env

`env_file` is only read on container creation — a plain `restart` won't pick up `.env.docker` changes.

```bash
docker compose up -d --force-recreate app nginx reverb horizon
```

## 5. Clear Laravel's config cache

```bash
docker exec -it laravel_app php artisan config:clear
docker exec -it laravel_app php artisan optimize:clear
```

## 6. Verify

```bash
curl -I https://random-words-here.trycloudflare.com
``` 

Should return `HTTP/2 200`. Then check the asset URLs are using `https://`:

```bash
curl -s https://random-words-here.trycloudflare.com | grep -o 'href="[^"]*\.css"'
```

If this shows `https://` links and the page loads with CSS intact in-browser, you're done.

---

## Troubleshooting checklist

If the page is broken/blank/unstyled, check in this order:

1. **`printenv | grep APP_URL` inside `laravel_app`** — confirms the container actually has the new tunnel URL loaded. If it still shows the old value, the container wasn't recreated (step 4).
2. **`curl -I <tunnel-url>`** — confirms the app is even reachable (200 vs 500/502).
3. **`docker exec -it laravel_app tail -50 storage/logs/laravel.log`** — check for PHP fatal errors. An empty response body usually means a fatal error killed output.
4. **CSS/JS loading as `http://` instead of `https://`** — means the proxy headers aren't reaching PHP. Confirm the nginx `fastcgi_param HTTP_X_FORWARDED_PROTO` line and Laravel's `trustProxies(at: '*')` are both present and the app/nginx containers were rebuilt/restarted after any changes to those files.

---