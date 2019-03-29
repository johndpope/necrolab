#!/bin/bash

cd /var/www

if [ ! -f .env ]
    printf "Copying .env.example to .env\n"
    cp .env.example .env
    
    printf "Generating Laravel key\n"
    php artisan key:generate
    
    printf "Running migrations\n"
    php artisan migrate
    
    printf "Running seeders\n"
    php artisan db:seed
fi
