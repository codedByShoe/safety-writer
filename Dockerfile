FROM dunglas/frankenphp

# Install system dependencies for single stage build
RUN apt-get update && apt-get install -y \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN install-php-extensions \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache \
    intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files first (needed for artisan)
COPY . .

ENV APP_ENV=production
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/data/database.sqlite

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && npm ci \
    && npm run build:ssr \
    && php artisan octane:install --server=frankenphp

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Create data directory for SQLite volume
RUN mkdir -p /data && chown -R www-data:www-data /data && chmod -R 775 /data

# Copy and set up entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8080/up || exit 1

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "octane:frankenphp", "--port=8080"]
