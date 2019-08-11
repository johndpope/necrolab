#!/bin/bash

if [ ! -f ./.env ]
then
    printf "========== Copying .env.example to .env. ==========\n"
    cp ./.env.example ./.env

    printf "========== Running composer install. ==========\n"
    composer install

    printf "========== Running npm install. ==========\n"
    npm install

    printf "========== Generating Laravel key. ==========\n"
    php artisan key:generate

    printf "========== Running migrations. ==========\n"
    php artisan migrate

    printf "========== Install Passport. ==========\n"
    php artisan passport:install

    printf "========== Generate Passport Keys. ==========\n"
    php artisan passport:keys

    printf "========== Running seeders. ==========\n"
    php artisan db:seed

    printf "========== Clearing cache and opcache. ==========\n"
    php artisan cache:opcache:clear
    php artisan cache:clear

    printf "========== Creating Steam schema. ==========\n"
    php artisan leaderboards:sources:create_schema --leaderboard_source=steam

    printf "========== Generating dates. ==========\n"
    php artisan dates:add_range --start_date=2014-07-01

    printf "========== Creating public storage. ==========\n"
    php artisan storage:link

    printf "========== Generating attributes json. ==========\n"
    php artisan attributes:generate_json

    printf "========== Running npm. ==========\n"
    npm run dev

    printf printf "========== All done! You can now navigate to https://local.necrolab.com ==========\n"
fi
