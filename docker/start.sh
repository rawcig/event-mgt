#!/bin/sh
set -e

# Render (and most PaaS) inject PORT at runtime, not build time —
# substitute it into the nginx template now.
export PORT=${PORT:-10000}
envsubst '${PORT}' < /etc/nginx/templates/nginx.conf.template > /etc/nginx/sites-enabled/default

# php artisan migrate --force

php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
