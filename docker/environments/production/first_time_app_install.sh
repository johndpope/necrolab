#!/bin/bash

if [ ! -f ./.env ]
then
    printf "========== Copying .env.example to .env. ==========\n"
    cp ./.env.example ./.env
    
    echo "========== Set Database Credentials =========="
    
    read -p 'Database Username: ' database_username
    
    while true; do
        read -sp 'Database Password: ' database_password
        echo
        read -sp 'Confirm Database Password: ' database_confirm_password
        echo
        [ "$database_password" = "$database_confirm_password" ] && break || echo "Password is incorrect. Please try again."
    done
    
    sed -i "s/DB_USERNAME\=.*/DB_USERNAME=$database_username/g" ./.env
    
    sed -i "s/DB_PASSWORD\=.*/DB_PASSWORD=$database_password/g" ./.env
    
    
    sed -i "s/APP_ENV\=.*/APP_ENV=production/g" ./.env
    
    sed -i "s/APP_DEBUG\=.*/APP_DEBUG=false/g" ./.env
    
    sed -i "s/APP_URL\=.*/APP_URL=https\:\/\/beta\.necrolab\.com/g" ./.env
   
    printf "========== Running composer install. ==========\n"
    composer install

    printf "========== Running npm install. ==========\n"
    npm install

    printf "========== Generating Laravel key. ==========\n"
    php artisan key:generate

    printf "========== Running migrations. ==========\n"
    php artisan migrate

    printf "========== Running seeders. ==========\n"
    php artisan db:seed

    printf "========== Clearing cache and opcache. ==========\n"
    php artisan cache:opcache:clear
    php artisan cache:clear

    printf "========== Creating Steam schema. ==========\n"
    php artisan leaderboards:sources:create_schema --leaderboard_source=steam

    printf "========== Generating dates. ==========\n"
    php artisan dates:add_range --start_date=2014-07-01

    printf "==========Creating public storage. ==========\n"
    php artisan storage:link

    printf "========== Generating attributes json. ==========\n"
    php artisan attributes:generate_json

    printf "========== Running npm. ==========\n"
    npm run production

    printf printf "========== All done! You can now navigate to https://www.necrolab.com ==========\n"
fi
