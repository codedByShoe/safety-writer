#!/bin/sh
set -e

# Create database file if it doesn't exist
if [ ! -f /data/database.sqlite ]; then
    echo "Creating SQLite database..."
    touch /data/database.sqlite
    chmod 664 /data/database.sqlite
fi

# Ensure proper permissions on data directory
chown -R www-data:www-data /data
chmod -R 775 /data

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan db:optimize
php artisan migrate --force
php artisan inertia:start-ssr >/proc/1/fd/1 2>/proc/1/fd/2 &

# Execute the CMD
exec "$@"
