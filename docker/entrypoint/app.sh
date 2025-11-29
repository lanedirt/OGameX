# Generate APP_KEY if not set or empty in the .env file
app_key=$(grep -E "^APP_KEY=" .env | cut -d '=' -f2 | tr -d '[:space:]' | tr -d '\r')
if [ -z "$app_key" ]; then
    echo "APP_KEY is empty or not set. Generating a new key..."
    php artisan key:generate --force
else
    echo "APP_KEY is set to: $app_key"
fi

php artisan migrate --force

# Only run caching in production
if [ "$IS_PRODUCTION" = true ]; then
    echo "Production environment: Caching configurations..."
    php artisan cache:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
fi

exec php-fpm
