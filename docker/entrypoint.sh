role=${CONTAINER_ROLE:-none}

if [ "$role" = "scheduler" ]; then
      while [ true ]
         do
           php /var/www/html/artisan schedule:run --verbose --no-interaction &
           sleep 60
         done
elif [ "$role" = "app" ]; then
      exec "php-fpm"
else
    echo "Could not match the container role \"$role\""
    exit 1
fi