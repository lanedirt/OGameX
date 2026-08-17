# Install OGameX (Docker)

Docker Compose is the **recommended, documented** install path. It is what CI tests on every build. This file is the full walkthrough of that setup. Short quick start: [README.md](../README.md#installation).

Advanced users can deploy without Docker. OGameX requires PHP ^8.5. See the list of requirements for Laravel 13.x and how to deploy to a server here: https://laravel.com/docs/13.x/deployment. That path is not walked through in this file.

## Contents

- [Prerequisites](#prerequisites)
- [What the stack runs](#what-the-stack-runs)
- [Development install](#development-install)
- [Production install](#production-install)
- [Verify the install](#verify-the-install)
- [After install](#after-install)
- [Configuration](#configuration)
- [Credentials and SSL](#credentials-and-ssl)
- [Upgrade](#upgrade)
- [Troubleshooting](#troubleshooting)
- [Developing after Docker install](#developing-after-docker-install)

## Prerequisites

- Git
- [Docker Engine](https://docs.docker.com/engine/install/) with Compose v2 (`docker compose`, not the old `docker-compose` binary)
- Enough RAM and disk for the **first** image build and Rust compile (small Docker Desktop memory defaults can run out of memory here)

`.env` is gitignored and is **not** in a fresh clone. Compose reads `.env` from the host when it starts, to interpolate host port mappings. Copy the example file **on the host before** `docker compose up`. If you wait until the container creates `.env` on first boot, ports 80/443 are already bound.

Default host ports (uncomment and set these in `.env` if they are already in use — for example a host nginx/Apache or MySQL):

| Variable | Default | Used by |
|----------|---------|---------|
| `HTTP_PORT` | 80 | Nginx |
| `HTTPS_PORT` | 443 | Nginx |
| `PHPMYADMIN_PORT` | 8080 | PhpMyAdmin |
| `REVERB_SERVER_PORT` | 8090 | Reverb (chat) |
| `DB_EXTERNAL_PORT` | 3306 | MariaDB (**development compose only**) |

In `.env.example` / `.env.example-prod`, `HTTP_PORT`, `HTTPS_PORT`, `PHPMYADMIN_PORT`, and `DB_EXTERNAL_PORT` are commented out. Commented lines are ignored by Compose (the YAML `:-` defaults apply). To change a port, uncomment the line and set a value. `REVERB_SERVER_PORT` is already set in `.env.example`. Keep `APP_URL` in sync with the URL you actually open, including scheme and port.

Linux is the path these instructions assume. On Windows and macOS, run the game through Docker Desktop (Linux containers). Do not compile the Rust battle engine on the host; that happens inside `ogamex-app`.

Windows note: the **development** compose file is slow because OPcache is off so PHP edits are live. For a usable frame rate on Windows, use the **production** compose file instead (OPcache on; PHP file edits are not instant).

The image already includes PHP 8.5-FPM, Composer, Rust/Cargo, and PHP FFI. Node/npm is **not** in the image. Operators use the compiled frontend assets as shipped. Contributors who change CSS/JS build on the host — see [CONTRIBUTING.md](../CONTRIBUTING.md).

## What the stack runs

| Service | Role | Default host ports |
|---------|------|--------------------|
| `ogamex-app` | PHP-FPM. First boot: Composer, `APP_KEY`, Rust compile, migrate | internal `:9000` |
| `ogamex-webserver` | Nginx | 80, 443 |
| `ogamex-db` | MariaDB 11.3 | 3306 (dev only) |
| `ogamex-scheduler` | `schedule:run` loop | — |
| `ogamex-queue-worker` | `queue:work` | — |
| `ogamex-reverb` | WebSockets (chat) | 8090 |
| `ogamex-phpmyadmin` | Database UI | 8080 |

First boot of `ogamex-app` can take **up to about 10 minutes** (Composer + Rust). Wait until that service is **healthy** before opening the site. The entrypoint is `docker/entrypoint.sh`.

## Development install

Use `docker-compose.yml`. Walk these steps on a **clean clone** in this order.

1. Clone the repository:

   ```bash
   git clone https://github.com/lanedirt/OGameX.git
   cd OGameX
   ```

2. Optional: for a game server you intend to keep, check out a **release tag** (`git checkout 0.14.0`, or the latest tag). Use `main` only if you want nightly builds (same split as [main.ogamex.dev](https://main.ogamex.dev) vs [release.ogamex.dev](https://release.ogamex.dev)).

3. Copy the example env file **on the host** (this file is gitignored; a clone does not contain `.env`):

   ```bash
   cp .env.example .env
   ```

   If a leftover `.env` already exists, it is **not** overwritten by the container later. Use a fresh copy when you want the example defaults.

4. If ports 80, 443, 8080, 8090, or 3306 are already in use on the host, uncomment and set the port variables in that `.env`, and set `APP_URL` to match. Example:

   ```bash
   HTTP_PORT=8081
   HTTPS_PORT=8443
   PHPMYADMIN_PORT=8082
   REVERB_SERVER_PORT=8091
   DB_EXTERNAL_PORT=3307
   APP_URL=http://localhost:8081
   ```

   Skip this step if the defaults are free.

5. Start the stack:

   ```bash
   docker compose up -d
   ```

   On first boot this command does **not** return immediately. Scheduler, queue worker, webserver, and Reverb wait until `ogamex-app` is healthy (Composer + Rust + migrate), which can take several minutes. That wait is expected.

6. Confirm `ogamex-app` is **healthy** and `ogamex-scheduler` plus `ogamex-queue-worker` are up:

   ```bash
   docker compose ps
   ```

7. Check the login page (use your `HTTP_PORT` if you changed it):

   ```bash
   curl -s -o /dev/null -w "%{http_code}\n" http://localhost/login
   ```

   A `200` means the web stack is up. Default URL: http://localhost (or `http://localhost:$HTTP_PORT`). Keep `APP_URL` in sync with the URL you actually use.

Then create an account and see [After install](#after-install).

Artisan must run **inside** the container (the host PHP is not the app runtime):

```bash
docker compose exec -it ogamex-app bash
# then: php artisan …
```

Or without a shell:

```bash
docker compose exec ogamex-app php artisan <command>
```

## Production install

Use `docker-compose.prod.yml`. It enables OPcache and does not publish the database port. Same idea as development: copy `.env` **on the host before** Compose starts.

**Caution:** the bundled production compose file is **not fully hardened**. The database root password defaults to `toor`. Review settings before binding this to a public address. See [Credentials and SSL](#credentials-and-ssl).

```bash
git clone https://github.com/lanedirt/OGameX.git
cd OGameX
# optional: git checkout <release-tag>
cp .env.example-prod .env
```

If ports 80, 443, 8080, or 8090 are already in use, uncomment and set `HTTP_PORT` / `HTTPS_PORT` / `PHPMYADMIN_PORT` / `REVERB_SERVER_PORT` in that `.env`, and set `APP_URL` to match (production defaults to HTTPS, so include `https://` and the HTTPS port if it is not 443). Then:

```bash
docker compose -f docker-compose.prod.yml up -d --build --force-recreate
```

Wait until `ogamex-app` is healthy and the scheduler and queue worker are up (`docker compose -f docker-compose.prod.yml ps`), then [verify](#verify-the-install).

- `APP_ENV=production` forces HTTPS. Open **https://localhost** (browser warning is expected: bundled nginx certs are self-signed).
- For HTTP against this compose file, set `APP_ENV=local` in `.env` and recreate the app container (production also caches config).
- PhpMyAdmin is bound to port 8080 but IP-allowlisted via `docker/phpmyadmin/.htaccess` (default `Allow from 1.1.1.1`). You will get 403 until you add your client IP, or you can leave 8080 unpublished.
- After first boot, production runs `config:cache` / `route:cache` / `view:cache`. Changing `.env` later requires recreating `ogamex-app` (or clearing those caches inside the container). Otherwise Laravel keeps stale config.

These instructions do not cover reverse proxies, Let’s Encrypt, or Redis. Use the compose files as shipped.

Artisan:

```bash
docker compose -f docker-compose.prod.yml exec ogamex-app php artisan <command>
```

## Verify the install

Wait until `ogamex-app` is healthy **and** the scheduler and queue worker are running:

```bash
docker compose ps
```

(Add `-f docker-compose.prod.yml` if you used the production file.)

Then check the login page (development):

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost/login
```

Production (self-signed cert):

```bash
curl -k -s -o /dev/null -w "%{http_code}\n" https://localhost/login
```

A `200` means the web stack is up. If you changed `HTTP_PORT` / `HTTPS_PORT`, use that port in the URL.

## After install

1. Register an account. The first **non-Legor** user is assigned the admin role and renamed `Admin`. A seeded **Legor** account (planet Arakis at 1:1:2) already exists from migrations; it does not count as that first human user. First login may ask you to pick a character class.
2. Forgot-password email is **not** fully wired. Reset a password from the container:

   ```bash
   docker compose exec ogamex-app php artisan ogamex:admin:reset-password {username-or-email}
   ```

   Default new password is `12345678`. Pass `--random` for a generated password.
3. PhpMyAdmin: http://localhost:8080 — server host `ogame-db` (compose network alias), user `root`, password `toor`.
4. Admin role:

   ```bash
   docker compose exec ogamex-app php artisan ogamex:admin:assign-role {username}
   docker compose exec ogamex-app php artisan ogamex:admin:remove-role {username}
   ```

5. Logs:

   ```bash
   docker compose logs -f ogamex-app
   ```

   Laravel logs also live in `storage/logs/` inside the app container.

6. Database data lives in the Docker volume `ogame-dbdata`. `docker compose down` **keeps** it. `docker compose down -v` **wipes** the database.
7. Stop / start with the same compose file you installed with:

   ```bash
   docker compose stop
   docker compose up -d
   ```

## Configuration

Set these in `.env` (copied from `.env.example` or `.env.example-prod` **before** the first `docker compose up`). Port variables are interpolated by Compose at start time.

| Variable | Notes |
|----------|--------|
| `APP_ENV` | `local` (dev) or `production` (forces HTTPS) |
| `APP_KEY` | Generated on first boot if empty |
| `APP_URL` | Must match the URL you visit, including scheme and port |
| `APP_DEBUG` | `true` in the dev example, `false` in prod |
| `HTTP_PORT` / `HTTPS_PORT` / `PHPMYADMIN_PORT` / `DB_EXTERNAL_PORT` / `REVERB_SERVER_PORT` | Host port mappings. Uncomment in `.env` to override defaults. |
| `DB_HOST` | `ogamex-db` (service name) |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | Default `laravel` / `root` / `toor` |
| `DISCORD_ALERT_WEBHOOK` | Optional |

Compose also sets the MariaDB root password (`MARIADB_ROOT_PASSWORD` in development, `MYSQL_ROOT_PASSWORD` in production) to `toor`. **`.env` and the compose database password must match.** Changing the password on an existing `ogame-dbdata` volume does not re-initialize MariaDB.

Do not casually change cache, session, or queue drivers. The bundled scheduler and queue worker assume the example files.

The production `.env` example sets `BROADCAST_DRIVER=log`. In-game chat over Reverb is wired in development (`.env.example` uses Reverb). Do not replace Reverb with a different WebSocket stack.

## Credentials and SSL

Before any public bind:

1. Change the default database password in **both** `.env` (`DB_PASSWORD`) and the compose database environment. To apply a new password on a fresh database you must create a **new** volume (that means `docker compose down -v`, which deletes game data).
2. Set `APP_URL` to the real URL.
3. Production PhpMyAdmin: put your client IP in `docker/phpmyadmin/.htaccess`, or do not publish port 8080.

Bundled nginx certificates in `nginx/ssl/` are self-signed with `CN=localhost` and are for local use only. To regenerate (macOS/Linux with OpenSSL):

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout nginx/ssl/nginx.key -out nginx/ssl/nginx.crt \
  -subj "/C=US/ST=State/L=City/O=Organization/OU=Unit/CN=localhost"
```

If `APP_URL` is a hostname, put that hostname in `CN=`. This is not a Let’s Encrypt or reverse-proxy guide.

## Upgrade

Use the same compose file you originally started with.

1. Stop containers:

   **Development:**

   ```bash
   docker compose down
   ```

   **Production:**

   ```bash
   docker compose -f docker-compose.prod.yml down
   ```

   This does **not** delete `ogame-dbdata`.

2. Update the code:

   ```bash
   git pull origin main
   ```

   or pin a release:

   ```bash
   git checkout 0.14.0   # replace with the latest release tag
   ```

3. Rebuild and start:

   **Development:**

   ```bash
   docker compose up -d --build --force-recreate --remove-orphans
   ```

   **Production:**

   ```bash
   docker compose -f docker-compose.prod.yml up -d --build --force-recreate --remove-orphans
   ```

The entrypoint runs migrations (and production config/route/view cache) on start. Then [verify](#verify-the-install) again.

## Troubleshooting

| Symptom | What to do |
|---------|------------|
| Site not up, or `vendor/autoload.php` missing | Wait until `ogamex-app` is healthy. First boot can take ~10 minutes. Watch `docker compose logs -f ogamex-app` (Composer or Rust still running). Do **not** run `composer install` on the host. |
| Wrong PHP version / `artisan` fails on the host | Run commands in the container: `docker compose exec -it ogamex-app bash`. The service name is `ogamex-app` (not `ogame-app`). |
| Port already in use (host web server or database) | Copy `.env.example` (or `.env.example-prod`) to `.env` **before** `docker compose up`. Uncomment and set `HTTP_PORT` / `HTTPS_PORT` / `DB_EXTERNAL_PORT` / etc. in that file, keep `APP_URL` in sync (including the port), then start Compose. If you already started with the defaults, `docker compose down` (without `-v`), edit `.env`, and `up -d` again. Do not only edit compose YAML. |
| Buildings or fleets not progressing | `ogamex-scheduler` and `ogamex-queue-worker` must be up (`docker compose ps`). |
| In-game chat not connecting | Reverb must be published on `REVERB_SERVER_PORT` (default 8090). Production example broadcasts to `log` as shipped. |
| Windows is very slow | Use production compose (OPcache). If `ogamex-app` is unhealthy, read `docker compose logs ogamex-app`. |
| Firefox `PR_END_OF_FILE_ERROR` / Chrome “connection closed” on production | Open **https://localhost** and accept the self-signed certificate. `APP_ENV=production` forces HTTPS. Do not run `php artisan serve`. |
| PhpMyAdmin 403 in production | Add your client IP to `docker/phpmyadmin/.htaccess`. |
| 500 / permission denied on `storage/logs` | Entrypoint now chowns `storage` and runs migrate as `www-data`. If old root-owned files remain: `docker compose exec ogamex-app chown -R www-data:www-data storage`. |
| Browser SSL warning | Expected with bundled certs. CI uses `curl -k`. |
| Rust / FFI errors | Confirm `storage/rust-libs/libbattle_engine_ffi.so` **inside** `ogamex-app` after boot. Do not compile Rust on a macOS/Windows host. |
| Leftover `.env` from the wrong example | Entrypoint will not overwrite it. Copy the correct example yourself, then recreate the app container. |
| Still stuck | Open a GitHub issue or ask on [Discord](https://discord.gg/HJ4QRxxB5N). Include `docker compose ps` and `docker compose logs ogamex-app`. |

## Developing after Docker install

Install with the development compose file, then follow [CONTRIBUTING.md](../CONTRIBUTING.md). Run Pint, Rector, PHPStan, and tests **inside** `ogamex-app`. Run `npm run dev` / `npm run build` on the host.
