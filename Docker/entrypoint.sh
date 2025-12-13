#!/bin/bash
set -e

run_as_owner() {
    su -appuser -c "$1"
}

#
# Wait for MySQL using netcat
#
echo "[entrypoint] Waiting for MySQL at ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
MAX_RETRIES=10
RETRY_COUNT=0

until nc -z "${DB_HOST:-mysql}" "${DB_PORT:-3306}"; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "[entrypoint] ERROR: MySQL not available after $MAX_RETRIES attempts" >&2
        exit 1
    fi
    echo "[entrypoint] MySQL not ready, retrying ($RETRY_COUNT/$MAX_RETRIES)..."
    sleep 2
done
echo "[entrypoint] MySQL is ready!"

#
# Optional DB migrations
#
if [ "${AUTO_FRESH_MIGRATIONS:-true}" = "true" ] && ! php artisan migrate:status --no-ansi 2>/dev/null | grep -q "Pending"; then
    echo "[entrypoint] Running DB migrations" >&2
    php artisan migrate --force || {
        echo "[entrypoint] Migration failed, exiting..." >&2
        exit 1
    }
fi

#
# Manage caches and logs
#
echo "[entrypoint] Clearing caches and logs"
php artisan optimize:clear
php artisan logs:clear

echo "[entrypoint] Caching application"
composer dump-autoload -o
php artisan optimize

#
# Run storage link
#
echo "[entrypoint] Storage link" >&2
php artisan storage:link

#
# Run frankenphp
#
echo "[entrypoint] Starting FrankenPHP" >&2
exec frankenphp run --config /etc/caddy/Caddyfile

export APP_URL="$(grep '^APP_URL=' .env | cut -d '=' -f2-)"

