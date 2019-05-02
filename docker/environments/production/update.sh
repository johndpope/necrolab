#!/bin/bash

if [ -f ./.env ]
then
    echo "========== Setting the site into maintenance mode. =========="
    php artisan down --message="NecroLab is currently down for maintenance." --retry=60

    echo "========== Running composer install. =========="
    composer install
    
    echo "========== Running npm install. =========="
    npm install
    
    echo "========== Clearing cache and opcache. =========="
    php artisan cache:opcache:clear
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    echo "========== Running migrations. =========="
    php artisan migrate
    
    echo "========== Generating attributes json. =========="
    php artisan attributes:generate_json
    
    echo "========== Caching the config. =========="
    php artisan config:cache
    
    echo "========== Caching routes. =========="
    php artisan route:cache
    
    echo "========== Optimizing Composer. =========="    
    composer dump-autoload -o
    
    echo "========== Restarting queue workers. =========="
    php artisan queue:restart
    
    echo "========== Running npm. =========="
    npm run production
    
    echo "========== Bringing the site out of maintenance mode. =========="
    php artisan up
    
    echo "========== All done! Navigate to https://beta.necrolab.com to test. =========="
fi
