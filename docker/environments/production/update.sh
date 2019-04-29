#!/bin/bash

if [ ! -f ./.env ]
then
    printf "========== Running composer install. ==========\n"
    composer install
    
    printf "========== Running npm install. ==========\n"
    npm install
    
    printf "========== Running migrations. ==========\n"
    php artisan migrate
    
    printf "========== Restarting queue workers. ==========\n"
    php artisan queue:restart
    
    printf "========== Clearing cache and opcache. ==========\n"
    php artisan cache:opcache:clear
    php artisan cache:clear
    
    printf "========== Generating attributes json. ==========\n"
    php artisan attributes:generate_json
    
    printf "========== Running npm. ==========\n"
    npm run production
    
    printf printf "========== All done! Navigate to https://beta.necrolab.com to test. ==========\n"
fi
