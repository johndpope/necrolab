<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'steam' => [
        'appid' => env('STEAM_APPID'),
        'client_id' => null,
        'client_username' => env('STEAM_CLIENT_USERNAME'),
        'client_password' => env('STEAM_CLIENT_PASSWORD'),
        'client_secret' => env('STEAM_API_KEY'),
        'leaderboard_xml_url' => env('STEAM_LEADERBOARD_XML_URL'),
        'client_executable_path' => env('STEAM_CLIENT_EXECUTABLE_PATH'),
        'redirect' => env('STEAM_REDIRECT_URI'),
        'max_valid_score' => env('MAX_VALID_SCORE')
    ],
];
