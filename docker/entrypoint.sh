#!/bin/sh

role=${CONTAINER_ROLE:-none}

if [ ! -f /var/www/.env ]; then
    if [ -f /var/www/.env.example ]; then
        cp /var/www/.env.example /var/www/.env
        echo ".env file not found, copied .env.example to .env"
    else
        echo "Error: .env and .env.example files not found. Please create an .env file." >&2
        exit 1
    fi
fi

# Extract environment information
is_production=false
if grep -q "^APP_ENV=production" .env; then
    is_production=true
fi

# Configure Git to trust the working directory
git config --global --add safe.directory /var/www

if [ "$role" = "scheduler" ]; then
    while true; do
        php /var/www/html/artisan schedule:run --verbose --no-interaction
        sleep 60
    done
elif [ "$role" = "queue" ]; then
      php /var/www/html/artisan queue:work --verbose --no-interaction
elif [ "$role" = "app" ]; then
    # Check APP_ENV and run appropriate composer install
    if [ "$is_production" = true ]; then
        echo "Production environment detected. Running composer install --no-dev..."
        composer install --no-dev
    else
        echo "Development environment detected. Running composer install..."
        composer install
    fi

    # Generate APP_KEY if not set or empty in the .env file
    app_key=$(grep -E "^APP_KEY=" .env | cut -d '=' -f2 | tr -d '[:space:]' | tr -d '\r')
    if [ -z "$app_key" ]; then
        echo "APP_KEY is empty or not set. Generating a new key..."
        php artisan key:generate --force
    else
        echo "APP_KEY is set to: $app_key"
    fi

    # Compile rust modules
    chmod +x ./rust/compile.sh
    ./rust/compile.sh

    # Run migrations
    php artisan migrate --force

    # Only run caching in production
    if [ "$is_production" = true ]; then
        echo "Production environment: Caching configurations..."
        php artisan cache:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
    fi

    exec php-fpm
else
    echo "Could not match the container role \"$role\""
    exit 1
fi