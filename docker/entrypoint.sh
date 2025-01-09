#!/bin/bash

role=${CONTAINER_ROLE:-none}

# Extract environment information
is_production=false
if grep -q "^APP_ENV=production" .env; then
    is_production=true
fi

# Configure Git to trust the working directory
git config --global --add safe.directory /var/www

if [[ "$role" == "scheduler" ]]; then
    while true; do
        php /var/www/html/artisan schedule:run --verbose --no-interaction &
        sleep 60
    done
elif [[ "$role" == "app" ]]; then
    # Check APP_ENV and run appropriate composer install
    if [[ "$is_production" == true ]]; then
        echo "Production environment detected. Running composer install --no-dev..."
        composer install --no-dev
    else
        echo "Development environment detected. Running composer install..."
        composer install
    fi

    # Generate APP_KEY if not set in the .env file
    if grep -q "^APP_KEY=" .env; then
        if [[ -z "$(grep "^APP_KEY=" .env | cut -d '=' -f2)" ]]; then
            echo "APP_KEY is empty. Generating a new key..."
            php artisan key:generate --force
        else
            echo "APP_KEY already set. Skipping key generation."
        fi
    else
        echo "APP_KEY not found in .env. Generating a new key..."
        php artisan key:generate --force
    fi

    # Run migrations
    php artisan migrate --force

    # Only run caching in production
    if [[ "$is_production" == true ]]; then
        echo "Production environment: Caching configurations..."
        php artisan cache:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
    fi

    # Source cargo environment before compiling rust modules to ensure cargo is available
    source $HOME/.cargo/env

    # Compile rust modules
    chmod +x ./rust/test_ffi/compile.sh
    chmod +x ./rust/battle_engine_ffi/compile.sh
    ./rust/test_ffi/compile.sh
    ./rust/battle_engine_ffi/compile.sh

    exec php-fpm
else
    echo "Could not match the container role \"$role\""
    exit 1
fi