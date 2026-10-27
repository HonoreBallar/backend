#!/bin/sh
set -e

# Caddy listens on the port given by the platform (Render sets PORT), plain HTTP:
# HTTPS is handled by the platform's proxy.
export SERVER_NAME=":${PORT:-80}"

# Apply pending database migrations before serving traffic
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

exec "$@"
