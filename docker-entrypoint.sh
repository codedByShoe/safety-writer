#!/bin/sh
set -e

# Function to handle shutdown
shutdown() {
    echo "Shutting down gracefully..."
    kill -TERM $SSR_PID 2>/dev/null || true
    kill -TERM $MAIN_PID 2>/dev/null || true
    wait $MAIN_PID 2>/dev/null || true
    wait $SSR_PID 2>/dev/null || true
    exit 0
}

# Trap shutdown signals
trap shutdown TERM INT

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

# Start SSR in background
php artisan inertia:start-ssr >/proc/1/fd/1 2>/proc/1/fd/2 &
SSR_PID=$!

# Start main process in background
"$@" &
MAIN_PID=$!

# Wait for main process
wait $MAIN_PID
