#!/bin/bash

cd ../../

if [ ! -f ./.env ]
    printf "Copying .env.example to .env\n"
    cp ./.env.example ./.env
    
    printf "Generating Laravel key.\n"
    php artisan key:generate
    
    printf "Running migrations.\n"
    php artisan migrate
    
    printf "Running seeders.\n"
    php artisan db:seed
    
    printf "Generating dates.\n"
    php artisan dates:add_range --start_date=2014-07-01
    
    printf "Creating public storage.\n"
    php artisan storage:link
    
    printf "Generating attributes json.\n"
    php artisan attributes:generate_json
    
    printf "Done!\n"
fi
